<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'telefono' => ['required', 'digits:9'],
            'direccion_envio' => ['required', 'string', 'max:255'],
            'direccion_facturacion' => ['required', 'string', 'max:255'],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'telefono' => $input['telefono'],
            'direccion_envio' => $input['direccion_envio'],
            'direccion_facturacion' => $input['direccion_facturacion'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
