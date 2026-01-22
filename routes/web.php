<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UrlController;

// Главная страница
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    // Веб-интерфейс для управления ссылками
    Route::get('/dashboard', [UrlController::class, 'dashboard'])->name('dashboard');
    Route::get('/urls/create', [UrlController::class, 'create'])->name('urls.create');
    Route::post('/urls/store', [UrlController::class, 'store'])->name('urls.store');
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

// Редирект по коротким ссылкам
Route::get('/{code}', [UrlController::class, 'run']);
