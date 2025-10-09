<?php

use Illuminate\Support\Facades\Route;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    /** Start Folder Routes */
    Route::prefix('admin/folders')->middleware(['auth'])->group(function () {
        Route::get('/', [\Modules\FileManager\Http\Controllers\Admin\FolderController::class, 'index']);
        Route::post('/', [\Modules\FileManager\Http\Controllers\Admin\FolderController::class, 'store']);
        Route::get('{id}', [\Modules\FileManager\Http\Controllers\Admin\FolderController::class, 'show'])->whereNumber('id');
        Route::put('{folder}', [\Modules\FileManager\Http\Controllers\Admin\FolderController::class, 'update'])->whereNumber('folder');
        Route::delete('{folder}', [\Modules\FileManager\Http\Controllers\Admin\FolderController::class, 'destroy'])->whereNumber('folder');
    });
    /** End Folder Routes */

    /** Start File Routes */
    Route::prefix('admin/files')->group(function () {
        Route::post('/', [\Modules\FileManager\Http\Controllers\Admin\FileController::class, 'store']);
        Route::get('{slug}/view', [\Modules\FileManager\Http\Controllers\Admin\FileController::class, 'show'])->whereAlphaNumeric('slug');
        Route::get('{slug}/download', [\Modules\FileManager\Http\Controllers\Admin\FileController::class, 'downloadFile'])->whereAlphaNumeric('slug');
        Route::delete('{file}', [\Modules\FileManager\Http\Controllers\Admin\FileController::class, 'destroy'])->whereNumber('file');
    });
    /** End File Routes */

    /** Start File Routes */
    Route::prefix('files')->group(function () {
        Route::post('/', [\Modules\FileManager\Http\Controllers\Client\FileController::class, 'store']);
        Route::get('{slug}', [\Modules\FileManager\Http\Controllers\Client\FileController::class, 'show'])->whereAlphaNumeric('slug');
        Route::get('{slug}/download', [\Modules\FileManager\Http\Controllers\Client\FileController::class, 'downloadFile'])->whereAlphaNumeric('slug');
        Route::delete('{file}', [\Modules\FileManager\Http\Controllers\Client\FileController::class, 'destroy'])->whereNumber('file');
    });
    /** End File Routes */
});

Route::prefix('v1')->group(function () {
    Route::get('files/{slug}/view', [\Modules\FileManager\Http\Controllers\Client\FileController::class, 'viewWithToken'])
        ->whereAlphaNumeric('slug')
        ->name('api.files.view-with-token');
});
