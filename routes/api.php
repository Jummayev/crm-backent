<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        Route::prefix('users')->group(function (): void {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user}', [UserController::class, 'show']);
        });

        // Manager and Admin routes
        Route::middleware(['role:Manager|Admin'])->group(function (): void {
            // Reports
            Route::prefix('reports')->group(function (): void {
                // Route::get('/', [ReportController::class, 'index']);
            });
        });

        // All authenticated users (Operator, Manager, Admin)
        Route::prefix('customers')->group(function (): void {
            // Route::get('/', [CustomerController::class, 'index']);
            // Route::post('/', [CustomerController::class, 'store'])->middleware('permission:create-customer');
        });

        Route::prefix('orders')->group(function (): void {
            // Route::get('/', [OrderController::class, 'index']);
            // Route::post('/', [OrderController::class, 'store'])->middleware('permission:create-order');
        });
    });
});
