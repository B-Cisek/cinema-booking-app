<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository
{
    public function isSeatBooked(string $screeningId, string $seatId): bool
    {
        return Booking::query()
            ->where('bookings.screening_id', $screeningId)
            ->whereHas('bookedSeats', fn ($query) => $query->where('seat_id', $seatId))
            ->exists();
    }
}
