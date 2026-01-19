<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\CommercialRedirectController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UrlController;

// Главная страница (публичная)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    // Веб-интерфейс для управления ссылками
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/urls', [UrlController::class, 'index'])->name('urls.index');
    Route::get('/urls/create', [UrlController::class, 'create'])->name('urls.create');
    Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');
    Route::get('/urls/{url}/stats', [UrlController::class, 'stats'])->name('urls.stats');
    Route::delete('/urls/{url}', [UrlController::class, 'destroy'])->name('urls.destroy');

    // Выход
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Регистрация и авторизация
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// API эндпоинты (для AJAX и внешних клиентов)
Route::prefix('api')->group(function () {
    Route::post('/shorten', [ShortUrlController::class, 'shorten']);
});

// Коммерческие ссылки (с ограничением частоты запросов)
Route::get('/commercial/{code}', [CommercialRedirectController::class, 'showRedirectPage'])->name('commercial.show')->middleware('throttle:30,1');
Route::get('/commercial-redirect/{code}', [CommercialRedirectController::class, 'performRedirect'])->name('commercial.redirect');

// Редирект по коротким ссылкам
Route::get('/{code}', [ShortUrlController::class, 'run']);
