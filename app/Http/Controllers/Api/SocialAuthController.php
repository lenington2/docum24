<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirige al provider OAuth (Google, Facebook, etc.)
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    /**
     * Callback del provider OAuth
     * El frontend redirige aquí después de la autenticación
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->user();
        } catch (\Exception $e) {
            return redirect(config('app.frontend_url', 'https://app.docum24.com')
                . '/login?error=social_auth_failed');
        }

        // Buscar usuario existente por provider_id o email
        $user = User::where("{$provider}_id", $socialUser->getId())->first();

        if (!$user) {
            // Buscar por email (ya se registró con email/password)
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Vincular la cuenta social al usuario existente
                $user->update([
                    "{$provider}_id" => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // Crear usuario nuevo
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => null,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    "{$provider}_id" => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Actualizar avatar
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }

        // Crear token Sanctum
        $token = $user->createToken('app_docum24')->plainTextToken;

        // Redirigir al frontend con el token
        $frontendUrl = config('app.frontend_url', 'https://app.docum24.com');

        return redirect("{$frontendUrl}/auth/callback?token={$token}&user=" . urlencode(json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ])));
    }

    /**
     * Login social desde el frontend (recibe token del provider directamente)
     * Para uso futuro con app nativa
     */
    public function tokenLogin(Request $request, string $provider)
    {
        $this->validateProvider($provider);

        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken($request->access_token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token non valido'], 401);
        }

        $user = User::where("{$provider}_id", $socialUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    "{$provider}_id" => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => null,
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    "{$provider}_id" => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            $user->update(['avatar' => $socialUser->getAvatar()]);
        }

        $token = $user->createToken('app_docum24')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    private function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(422, 'Provider non supportato');
        }
    }
}
