<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Traits\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthService $authService) {}

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

            return $this->successResponse([
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
            return $this->validationErrorResponse($e->errors(), 'Login failed');
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'username' => $request->user()->username,
            'email' => $request->user()->email,
        ]);
    }
}
