<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user and return access token.
     *
     * @return array{user: User, access_token: string, token_type: string}
     *
     * @throws ValidationException
     */
    public function login(string $username, string $password): array
    {
        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('API Token')->accessToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout user by revoking token.
     */
    public function logout(User $user): bool
    {
        return $user->token()->revoke();
    }
}
