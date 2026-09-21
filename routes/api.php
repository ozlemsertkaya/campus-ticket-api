<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
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
    Route::get('/tickets/{ticket}/messages', [TicketController::class, 'getMessages']);
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus']);

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
// En alta ekle (test için auth dışı bırakıyoruz):
Route::post('/test-ticket', [TicketController::class, 'create']);
Route::get('/seed-db-emergency', function () {
    // 1. Kategoriyi ekle
    DB::statement("
        INSERT INTO categories (id, name, created_at, updated_at) 
        VALUES (1, 'Genel Arıza / Teknik', NOW(), NOW()) 
        ON CONFLICT (id) DO NOTHING;
    ");

    // 2. Öncelikleri ekle (level sütunu ile birlikte)
    DB::statement("
        INSERT INTO priorities (id, name, level, created_at, updated_at) VALUES 
        (1, 'Düşük', 1, NOW(), NOW()),
        (2, 'Orta', 2, NOW(), NOW()),
        (3, 'Yüksek', 3, NOW(), NOW()),
        (4, 'Acil', 4, NOW(), NOW())
        ON CONFLICT (id) DO NOTHING;
    ");

    // 3. PostgreSQL sayaçlarını sıfırla
    try {
        DB::statement("SELECT setval(pg_get_serial_sequence('categories', 'id'), COALESCE((SELECT MAX(id) FROM categories), 1));");
        DB::statement("SELECT setval(pg_get_serial_sequence('priorities', 'id'), COALESCE((SELECT MAX(id) FROM priorities), 1));");
    } catch (\Throwable $e) {
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Kategoriler ve öncelikler başarıyla veritabanına yazıldı!'
    ]);
});
