<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
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

    public function bookingNumberExists(string $bookingNumber): bool
    {
        return Booking::query()
            ->where('bookings.booking_number', $bookingNumber)
            ->exists();
    }

    public function create(
        string $bookingNumber,
        string $screeningId,
        string $customerEmail,
        PaymentMethod $paymentMethod,
        ?string $userId = null,
        ?string $guestId = null,
        BookingStatus $status = BookingStatus::PENDING,
    ): Booking {
        return Booking::query()->create([
            'screening_id' => $screeningId,
            'booking_number' => $bookingNumber,
            'status' => $status,
            'customer_email' => $customerEmail,
            'user_id' => $userId,
            'guest_id' => $guestId,
            'payment_method' => $paymentMethod,
        ]);
    }
}
