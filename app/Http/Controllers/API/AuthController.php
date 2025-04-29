<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->email, $request->password);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $result['token'],
            'user'    => $result['user'],
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
