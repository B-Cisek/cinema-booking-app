<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ScreeningReservationController;
use App\Http\Controllers\ScreeningReservationSuccessController;
use App\Http\Controllers\ScreeningReservationSummaryController;
use App\Http\Controllers\SeatHoldController;
use App\Http\Controllers\SeatReleaseController;
use App\Http\Controllers\SelectCinemaController;
use App\Http\Controllers\StoreScreeningReservationController;
use App\Http\Middleware\EnsureCinemaSelected;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::middleware(EnsureCinemaSelected::class)->group(function (): void {
    Route::get('/screenings/{screening}/reservation', ScreeningReservationController::class)
        ->name('screenings.reservation');

    Route::post('/screenings/{screening}/reservation', StoreScreeningReservationController::class)
        ->name('screenings.book');

    Route::get('/screenings/{screening}/reservation/summary', ScreeningReservationSummaryController::class)
        ->name('screenings.reservation-summary');

    Route::post('/screenings/seat-hold', SeatHoldController::class)
        ->name('screenings.seat-hold');

    Route::post('/screenings/seat-release', SeatReleaseController::class)
        ->name('screenings.seat-release');
});

Route::get('/screenings/{screening}/reservation/success/{booking}', ScreeningReservationSuccessController::class)
    ->name('screenings.reservation-success');

Route::post('/cinemas/select', SelectCinemaController::class)
    ->name('cinemas.select');
