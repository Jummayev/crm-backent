<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->input('username'),
                $request->input('password')
            );

            return okResponse([
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'username' => $result['user']->username,
                    'email' => $result['user']->email,
                ],
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type'],
            ], 'Login successful');
        } catch (ValidationException $e) {
            return invalidData('Login failed', $e->errors());
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return okResponse(null, 'Logout successful');
    }

    /**
     * Get an authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return okResponse([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'username' => $request->user()->username,
            'email' => $request->user()->email,
        ]);
    }
}
