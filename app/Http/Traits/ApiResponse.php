<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     */
    protected function successResponse(mixed $data = null,
        string $message = 'Success', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = JsonResponse::HTTP_BAD_REQUEST, mixed $errors = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a created JSON response.
     */
    protected function createdResponse(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, JsonResponse::HTTP_CREATED);
    }

    /**
     * Return a no content JSON response.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Return a not found JSON response.
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * Return an unauthorized JSON response.
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Return a forbidden JSON response.
     */
    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, JsonResponse::HTTP_FORBIDDEN);
    }

    /**
     * Return a validation error JSON response.
     */
    protected function validationErrorResponse(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }
}
