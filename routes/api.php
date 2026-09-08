<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttachmentController;



Route::post('/tickets', [TicketController::class, 'create']);
Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve']);
Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);
Route::post('/tickets/{ticket}/messages', [TicketController::class, 'addMessage']);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('priorities', PriorityController::class);
Route::apiResource('customers', CustomerController::class);
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
Route::apiResource('users', UserController::class);
Route::post('/tickets/{ticket}/attachments', [AttachmentController::class, 'store']);
Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); //bu endpointe sadece geçerli bir token ile girilebilir diyor.

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
