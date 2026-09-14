<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketMessageController;


//public rotalar
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'create']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::post('/tickets/{ticket}/messages', [TicketController::class, 'addMessage']);

    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);

    Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index']);
    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store']);

    Route::post('/tickets/{ticket}/attachments', [AttachmentController::class, 'store']);
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('priorities', PriorityController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('users', UserController::class);
});
