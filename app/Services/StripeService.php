<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plane;
use App\Models\Suscripcion;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Checkout\Session;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // Obtener o crear customer en Stripe
    public function getOrCreateCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name'  => $user->name,
            'metadata' => ['user_id' => $user->id],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer->id;
    }

    // Crear sesión de checkout
    public function crearCheckout(User $user, string $planNombre): string
    {
        $priceId = match($planNombre) {
            'Basic' => config('services.stripe.price_basic'),
            'Pro'   => config('services.stripe.price_pro'),
            default => throw new \Exception('Piano non valido'),
        };

        $customerId = $this->getOrCreateCustomer($user);

        $session = Session::create([
            'customer'            => $customerId,
            'mode'                => 'subscription',
            'payment_method_types' => ['card'],
            'line_items'          => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => url('/checkout/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'  => url('/checkout/cancel'),
            'metadata'    => [
                'user_id'    => $user->id,
                'plan_nombre' => $planNombre,
            ],
        ]);

        return $session->url;
    }

    // Cancelar suscripción
    public function cancelarSuscripcion(User $user): bool
    {
        $sus = Suscripcion::where('user_id', $user->id)
            ->where('estado', 'activa')
            ->first();

        if (!$sus || !$sus->stripe_subscription_id) return false;

        \Stripe\Subscription::cancel($sus->stripe_subscription_id);

        $sus->update(['estado' => 'cancelada']);

        return true;
    }
}
