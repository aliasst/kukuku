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

    // AJAX подарить подарок
    Route::post('/send-gift', [EventController::class, 'sendGift'])->name('send.gift');

    // AJAX регистрация на событие
    Route::post('/events/ajax-register', [EventParticipantController::class, 'ajaxRegister'])->name('events.ajax.register');

    // Подмигивания
    Route::post('/send-wink', [EventController::class, 'sendWink'])->name('send.wink');
    Route::post('/wink/{winkId}/accept', [EventController::class, 'acceptWink'])->name('wink.accept');
    Route::post('/wink/{winkId}/ignore', [EventController::class, 'ignoreWink'])->name('wink.ignore');
    Route::get('/my-winks', [EventController::class, 'getMyWinks'])->name('my.winks');
    Route::get('/unviewed-winks-count', [EventController::class, 'getUnviewedWinksCount'])->name('unviewed.winks');

    // Подарки
    Route::post('/gift/{giftId}/accept', [EventController::class, 'acceptGift'])->name('gift.accept');
    Route::post('/gift/{giftId}/ignore', [EventController::class, 'ignoreGift'])->name('gift.ignore');
    Route::get('/my-gifts', [EventController::class, 'getMyGifts'])->name('my.gifts');
    Route::get('/unviewed-gifts-count', [EventController::class, 'getUnviewedGiftsCount'])->name('unviewed.gifts');

    // Маршруты для профиля
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

