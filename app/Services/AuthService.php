<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthService
{
    public function login($email, $password)
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Kredensial tidak valid.',
            ];
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }
}
