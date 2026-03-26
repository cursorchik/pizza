<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PizzaController;
use App\Http\Controllers\UserController;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/pizzas', [PizzaController::class, 'index']);
Route::get('/pizzas/{pizza}', [PizzaController::class, 'show']);
Route::get('/drinks', [DrinkController::class, 'index']);
Route::get('/drinks/{drink}', [DrinkController::class, 'show']);

// Аутентификация
Route::prefix('auth')->group(function ()
{
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Админские действия
Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function ()
{
    Route::apiResource('pizzas', PizzaController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('drinks', DrinkController::class)->only(['store', 'update', 'destroy']);
});

// Заказы (пользовательские)
Route::middleware('auth:api')->group(function ()
{
    Route::get('/orders/history', [UserController::class, 'history']);
    Route::get('/user/orders', [UserController::class, 'orders']);
    Route::apiResource('orders', OrderController::class)->only(['store', 'show', 'update']);
});

Route::prefix('cart')->middleware(StartSession::class)->group(function ()
{
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'add']);
    Route::patch('/{type}/{id}', [CartController::class, 'update']);
    Route::delete('/{type}/{id}', [CartController::class, 'destroy']);
});


























