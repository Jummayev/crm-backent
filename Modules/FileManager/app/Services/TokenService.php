<?php

namespace Modules\FileManager\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;
use Modules\FileManager\Models\File;

class TokenService
{
    private static function getEncrypter(): Encrypter
    {
        $key = config('app.file_token_key') ?: config('app.key');

        return new Encrypter($key, config('app.cipher'));
    }

    public static function generateToken(File $file, int $userId, int $expiresInDays = 7): string
    {
        $payload = [
            'hash' => Str::random(6),
            'file_id' => $file->id,
            'user_id' => $userId,
            'ip' => request()->ip(),
            'expires_at' => now()->addDays($expiresInDays)->timestamp,
        ];

        return self::getEncrypter()->encryptString(json_encode($payload));
    }

    public static function decryptToken(string $token): ?array
    {
        try {
            $decrypted = self::getEncrypter()->decryptString($token);
            $payload = json_decode($decrypted, true);

            if (! $payload || ! isset($payload['file_id'], $payload['user_id'], $payload['expires_at'])) {
                return null;
            }

            if ($payload['expires_at'] < now()->timestamp) {
                return null;
            }

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function validateToken(string $token, int $fileId): ?array
    {
        $payload = self::decryptToken($token);

        if (! $payload) {
            return null;
        }

        if ($payload['file_id'] !== $fileId) {
            return null;
        }

        return $payload;
    }
}
