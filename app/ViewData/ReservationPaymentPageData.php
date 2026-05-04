<?php

declare(strict_types=1);

namespace App\ViewData;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Screening;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;

readonly class ReservationPaymentPageData
{
    public function build(
        Booking $booking,
        Screening $screening,
        PaymentMethod $paymentMethod,
    ): array {
        $booking->loadMissing([
            'screening.hall.cinema',
            'screening.movie',
            'bookedSeats.seat',
        ]);

        if ($booking->screening_id !== $screening->getKey()) {
            throw new ModelNotFoundException;
        }

        return [
            'booking' => [
                'id' => $booking->getKey(),
                'number' => $booking->booking_number,
                'email' => $booking->customer_email,
                'status' => $booking->status->value,
                'total' => $booking->bookedSeats->sum('price'),
            ],
            'paymentMethod' => [
                'code' => $paymentMethod->value,
                'label' => $paymentMethod->label(),
                'description' => $paymentMethod->description(),
            ],
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $booking->screening->starts_at->format('H:i'),
                'ends_at' => $booking->screening->ends_at->format('H:i'),
                'date' => $booking->screening->starts_at->locale(App::currentLocale())->translatedFormat('j F Y'),
                'movie' => [
                    'title' => $booking->screening->movie->title,
                ],
            ],
            'isAlreadyPaid' => $booking->status === BookingStatus::CONFIRMED,
        ];
    }
}
