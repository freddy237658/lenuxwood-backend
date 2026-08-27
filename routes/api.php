<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\RealisationController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\DashboardController;

use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});




Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [DashboardController::class, 'stats']);

    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
});



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});





Route::post('/quotes', [QuoteController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me/quotes', [QuoteController::class, 'myQuotes']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/quotes', [QuoteController::class, 'index']);
    Route::get('/quotes/{quote}', [QuoteController::class, 'show']);
    Route::patch('/quotes/{quote}', [QuoteController::class, 'updateStatus']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);
});





Route::post('/payments/webhook', [PaymentController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/me/orders', [OrderController::class, 'myOrders']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/me/payments', [PaymentController::class, 'myPayments']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    Route::get('/payments', [PaymentController::class, 'index']);
});





Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me/conversation', [ConversationController::class, 'myConversation']);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations/find-or-create', [ConversationController::class, 'findOrCreateForUser']);
});




Route::get('/team', [TeamMemberController::class, 'index']);
Route::get('/realisations', [RealisationController::class, 'index']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/team', [TeamMemberController::class, 'store']);
    Route::post('/team/{teamMember}', [TeamMemberController::class, 'update']);
    Route::delete('/team/{teamMember}', [TeamMemberController::class, 'destroy']);

    Route::post('/realisations', [RealisationController::class, 'store']);
    Route::post('/realisations/{realisation}', [RealisationController::class, 'update']);
    Route::delete('/realisations/{realisation}', [RealisationController::class, 'destroy']);
});