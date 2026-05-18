<?php

declare(strict_types=1);

namespace App\Commands;

use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Models\Booking;

class ConfirmReservationPayment
{
    public function handle(array $order): void
    {
        $booking = Booking::query()->findOrFail($order['extOrderId']);

        if ($order['status'] === 'COMPLETED') {
            $booking->markAsConfirmed();
            BookingConfirmed::dispatch($booking);
        }

        if ($order['status'] === 'CANCELED') {
            $booking->markAsCancelled();
            BookingCancelled::dispatch($booking);
            // TODO: handle relese seats
        }
    }
}
