<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);
Route::get('/v1/health', [SyncController::class, 'health']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/sync/pull', [SyncController::class, 'pull']);
    Route::post('/v1/sync/push', [SyncController::class, 'push']);
});
