<?php

namespace App\Http\Controllers;

use App\Models\Plane;
use App\Models\Suscripcion;
use App\Models\User;
use App\Services\StripeService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeController extends Controller
{
    protected StripeService $stripe;
    protected TokenService  $tokens;

    public function __construct()
    {
        $this->stripe = new StripeService();
        $this->tokens = new TokenService();
    }

    // Redirigir al checkout de Stripe
    public function checkout(Request $request, string $plan)
    {
        $validPlans = ['Basic', 'Pro'];
        if (!in_array($plan, $validPlans)) {
            return response()->json(['error' => 'Piano non valido'], 400);
        }

        try {
            $url = $this->stripe->crearCheckout(Auth::user(), $plan);

            if ($request->ajax()) {
                return response()->json(['url' => $url]);
            }

            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Errore nel processo di pagamento.'], 500);
            }
            return back()->with('error', 'Errore nel processo di pagamento.');
        }
    }

    // Página de éxito
    public function success(Request $request)
    {
        return view('checkout.success');
    }

    // Página de cancelación
    public function cancel()
    {
        return view('checkout.cancel');
    }

    // Webhook de Stripe
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook')
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response('Webhook error', 400);
        }

        switch ($event->type) {

            // Pago completado → activar suscripción
            case 'checkout.session.completed':
                $session  = $event->data->object;
                $userId   = $session->metadata->user_id;
                $planNombre = $session->metadata->plan_nombre;
                $subId    = $session->subscription;

                $user = User::find($userId);
                $plan = Plane::where('nombre', $planNombre)->first();

                if ($user && $plan) {
                    // Desactivar suscripción anterior
                    Suscripcion::where('user_id', $userId)
                        ->where('estado', 'activa')
                        ->update(['estado' => 'expirada']);

                    // Crear nueva suscripción
                    Suscripcion::create([
                        'user_id'                => $userId,
                        'plan_id'                => $plan->id,
                        'tokens_usados'          => 0,
                        'tokens_limite'          => $plan->tokens_mes,
                        'fecha_inicio'           => now()->toDateString(),
                        'fecha_fin'              => now()->addDays(30)->toDateString(),
                        'estado'                 => 'activa',
                        'stripe_subscription_id' => $subId,
                        'stripe_price_id'        => $session->line_items?->data[0]?->price?->id ?? null,
                    ]);
                }
                break;

            // Renovación mensual → resetear tokens
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $subId   = $invoice->subscription;

                $sus = Suscripcion::where('stripe_subscription_id', $subId)->first();
                if ($sus) {
                    $sus->update([
                        'tokens_usados' => 0,
                        'fecha_fin'     => now()->addDays(30)->toDateString(),
                        'estado'        => 'activa',
                    ]);
                }
                break;

            // Pago fallido → marcar como expirada
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $subId   = $invoice->subscription;

                Suscripcion::where('stripe_subscription_id', $subId)
                    ->update(['estado' => 'expirada']);
                break;

            // Suscripción cancelada
            case 'customer.subscription.deleted':
                $sub = $event->data->object;
                Suscripcion::where('stripe_subscription_id', $sub->id)
                    ->update(['estado' => 'cancelada']);
                break;
        }

        return response('OK', 200);
    }
}
