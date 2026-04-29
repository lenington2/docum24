<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'      => $this->passwordRules(),
            'business_type' => ['nullable', 'string', 'max:255'],
            'ai_prompt'     => ['nullable', 'string'],
            'terms'         => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name'          => $input['name'],
            'email'         => $input['email'],
            'password'      => Hash::make($input['password']),
            'business_type' => $input['business_type'] ?? null,
            'ai_prompt'     => $input['ai_prompt'] ?? null,
        ]);

        // Crear Trial siempre
        (new \App\Services\TokenService())->crearTrialParaUsuario($user);

        // Guardar plan pendiente en session si eligió Basic o Pro
        if (!empty($input['selected_plan']) && $input['selected_plan'] !== 'Trial') {
            session(['pending_plan' => $input['selected_plan']]);
        }

        return $user;
    }
}
