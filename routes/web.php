<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\EventController;
use App\Http\Controllers\Cabinet\EventParticipantController;
use App\Http\Controllers\Cabinet\ProfileController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


// Гостевые маршруты (только для неавторизованных)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Восстановление пароля
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// Защищённые маршруты (только для авторизованных)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});




Route::prefix('cabinet')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('cabinet.dashboard');

    // Маршруты для мероприятий
    Route::get('/events/upcoming', [EventController::class, 'upcoming'])->name('events.upcoming'); // Будущие события
    Route::get('/events/past', [EventController::class, 'past'])->name('events.past'); // Прошедшие события

    // Получить участников мероприятия (для модального окна)
    Route::get('/events/participants/{event}', [EventController::class, 'getParticipants'])
        ->name('events.participants')
        ->where('event', '[0-9]+');

    // AJAX регистрация на событие
    Route::post('/events/ajax-register', [EventParticipantController::class, 'ajaxRegister'])->name('events.ajax.register');

    // Маршруты для профиля
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

