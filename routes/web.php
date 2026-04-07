<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScreeningReservationController;
use App\Http\Controllers\SeatHoldController;
use App\Http\Controllers\SeatReleaseController;
use App\Http\Controllers\SelectCinemaController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/screenings/{screening}/reservation', ScreeningReservationController::class)
    ->name('screenings.reservation');
Route::post('/cinemas/select', SelectCinemaController::class)
    ->name('cinemas.select');

Route::post('/screenings/seat-hold', SeatHoldController::class)
    ->name('screenings.seat-hold');

Route::post('/screenings/seat-release', SeatReleaseController::class)
    ->name('screenings.seat-release');
