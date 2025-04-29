<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password): array
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

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
