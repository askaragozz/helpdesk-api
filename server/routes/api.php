<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TicketController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign']);

    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
    Route::get('/tickets/{ticket}/status-history', [TicketController::class, 'statusHistory']);

    Route::get('/tickets/{ticket}/comments', [TicketController::class, 'listComments']);
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment']);
    Route::post('/tickets/{ticket}/comments/read', [TicketController::class, 'markCommentsRead']);
});

