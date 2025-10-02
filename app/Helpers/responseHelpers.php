<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

const RESPONSE_NOTIFY_SUCCESS = 'success';
const RESPONSE_NOTIFY_ERROR = 'error';
function createdResponse($data = null, string $userMessage = 'Successfully created', $status = RESPONSE_NOTIFY_SUCCESS): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_CREATED);
}

function okResponse($data = null, string $userMessage = 'Success', $status = RESPONSE_NOTIFY_SUCCESS, array $metaData = []): JsonResponse
{
    return response()->json(array_merge([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], $metaData), Response::HTTP_OK);
}

function okWithPaginateResponse(LengthAwarePaginator $paginator, string $userMessage = 'Success', $status = RESPONSE_NOTIFY_SUCCESS): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $paginator->items(),
        'meta' => [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ],
    ], Response::HTTP_OK);
}

function badRequestResponse(string $userMessage = 'Invalid request', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_BAD_REQUEST);
}

function invalidData(string $userMessage = 'The given data is invalid', $data = [], $status = RESPONSE_NOTIFY_ERROR, array $extraData = []): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'errors' => $data,
        ...$extraData,
    ], Response::HTTP_UNPROCESSABLE_ENTITY);
}

function unauthorizedRequestResponse(string $userMessage = 'Please login to access this resource', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_UNAUTHORIZED);
}

function forbiddenRequestResponse(string $userMessage = 'You do not have permission to perform this action', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_FORBIDDEN);
}

function notFoundRequestResponse(string $userMessage = 'Resource not found', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_NOT_FOUND);
}

function methodNotAllowedRequestResponse(string $userMessage = 'This method is not allowed', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_METHOD_NOT_ALLOWED);
}

function tooManyRequestsResponse(string $userMessage = 'Too many requests. Please wait a moment', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_TOO_MANY_REQUESTS);
}

function serverErrorResponse(string $userMessage = 'Server error occurred. Please try again', $data = [], $status = RESPONSE_NOTIFY_ERROR): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => $status,
        'data' => $data,
    ], Response::HTTP_INTERNAL_SERVER_ERROR);
}

function errorResponse(string $userMessage = 'An error occurred', string $debugMessage = '', $data = [], ?int $statusCode = null): JsonResponse
{
    $response = [
        'userMessage' => $userMessage,
        'status' => 'error',
        'data' => $data,
    ];

    if (config('app.debug') && $debugMessage) {
        $response['message'] = $debugMessage;
    }

    return response()->json($response, $statusCode ?? Response::HTTP_INTERNAL_SERVER_ERROR);
}

function postTooLargeResponse(string $userMessage = 'The uploaded file is too large', $data = []): JsonResponse
{
    return response()->json([
        'userMessage' => $userMessage,
        'status' => 'error',
        'data' => $data,
    ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
}
