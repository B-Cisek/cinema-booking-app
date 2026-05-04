<?php

declare(strict_types=1);

namespace App\Commands;

use App\Enums\BookingStatus;
use App\Mail\SendTicket;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;

class ConfirmScreeningReservationPayment
{
    public function handle(Booking $booking): Booking
    {
        if ($booking->status === BookingStatus::CONFIRMED) {
            return $booking;
        }

        $booking
            ->fill([
                'status' => BookingStatus::CONFIRMED,
            ])
            ->save();

        $booking->loadMissing([
            'screening.movie',
            'screening.hall.cinema',
            'bookedSeats.seat',
        ]);

        Mail::to($booking->customer_email)->queue(new SendTicket($booking));

        return $booking;
    }
}
