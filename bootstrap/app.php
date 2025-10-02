<?php

declare(strict_types=1);

use App\Http\Middleware\Cors;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            Cors::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Authentication Exception
        $exceptions->render(function (AuthenticationException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return unauthorizedRequestResponse();
            }

            return null;
        });

        // Authorization Exception
        $exceptions->render(function (AuthorizationException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return forbiddenRequestResponse();
            }

            return null;
        });

        // Validation Exception
        $exceptions->render(function (ValidationException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return invalidData($e->getMessage(), $e->errors());
            }

            return null;
        });

        // Not Found HTTP Exception
        $exceptions->render(function (NotFoundHttpException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return notFoundRequestResponse();
            }

            return null;
        });

        // Model Not Found Exception
        $exceptions->render(function (ModelNotFoundException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return notFoundRequestResponse('The requested resource was not found');
            }

            return null;
        });

        // Method Not Allowed Exception
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return methodNotAllowedRequestResponse('This method is not allowed for this endpoint');
            }

            return null;
        });

        // Too Many Requests Exception
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return tooManyRequestsResponse();
            }

            return null;
        });

        // Post Too Large Exception
        $exceptions->render(function (PostTooLargeException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                return postTooLargeResponse();
            }

            return null;
        });

        // Database Query Exception
        $exceptions->render(function (QueryException $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                $userMessage = 'A database error occurred. Please try again';
                $debugMessage = $e->getMessage();

                return errorResponse($userMessage, $debugMessage);
            }

            return null;
        });

        // Generic Throwable Exception
        $exceptions->render(function (Throwable $e, Request $request): ?Response {
            if ($request->is('api/*')) {
                $userMessage = 'An unexpected error occurred. Please try again';
                $debugMessage = $e->getMessage();

                return errorResponse($userMessage, $debugMessage);
            }

            return null;
        });
    })->create();
