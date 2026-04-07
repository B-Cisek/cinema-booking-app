<?php

declare(strict_types=1);

namespace App\Services\CinemaHall;

use App\Models\Booking;
use App\Models\Seat as SeatModel;
use App\Repositories\ScreeningRepository;
use App\Services\SeatHoldStore;

final readonly class CinemaHallFactory
{
    public function __construct(
        private ScreeningRepository $screeningRepository,
        private SeatHoldStore $seatHoldStore,
    ) {}

    public function forScreening(string $screeningId): Layout
    {
        $screening = $this->screeningRepository->getById($screeningId);

        $bookedSeatIds = $screening->bookings
            ->flatMap(fn (Booking $booking) => $booking->bookedSeats()->pluck('seat_id'));
        $heldSeatIds = $this->seatHoldStore->heldSeatIds(
            cinemaId: $screening->hall->cinema_id,
            screeningId: $screeningId,
        );
        $unavailableSeatIds = $bookedSeatIds->merge($heldSeatIds)->unique();

        return new Layout(
            $screening->hall->seats->map(fn (SeatModel $seat) => new Seat(
                id: $seat->id,
                row: $seat->row_label,
                seatNumber: $seat->seat_number,
                seatType: $seat->seat_type,
                posX: $seat->pos_x,
                posY: $seat->pos_y,
                isActive: $seat->is_active,
                isBooked: $unavailableSeatIds->contains($seat->getKey()),
            ))
        );
    }
}
