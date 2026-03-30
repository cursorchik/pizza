<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

// Публичные маршруты
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Аутентификация
Route::prefix('auth')->group(function ()
{
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    Route::middleware('auth:api')->group(function ()
    {
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    });
});

// Корзина (сессия)
Route::prefix('cart')->middleware(StartSession::class)->group(function ()
{
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
});

// Пользовательские заказы
Route::middleware('auth:api')->middleware(StartSession::class)->group(function ()
{
    Route::get('/user/orders', [UserController::class, 'orders'])->name('user.orders');
    Route::get('/orders/history', [UserController::class, 'history'])->name('orders.history');
    Route::apiResource('orders', OrderController::class)->only(['store', 'show', 'update']);
});

// Админские маршруты (CRUD продуктов)
Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function ()
{
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});
