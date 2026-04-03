<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScreeningReservationController;
use App\Http\Controllers\SelectCinemaController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/screenings/{screening}/reservation', ScreeningReservationController::class)
    ->name('screenings.reservation');
Route::post('/cinemas/select', SelectCinemaController::class)
    ->name('cinemas.select');
