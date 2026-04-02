<?php

namespace App\Services;

use App\Data\SeatMap;
use App\Data\SeatView;
use App\Enums\BookingStatus;
use App\Enums\SeatType;
use App\Models\Screening;
use App\Models\Seat;
use App\Repositories\ScreeningRepository;

final class ScreeningSeatMap
{
    public function __construct(private readonly ScreeningRepository $screeningRepository) {}

    public function for(string $screeningId): SeatMap
    {
        $screening = Screening::query()
            ->where('id', $screeningId)
            ->firstOrFail();

        $bookedSeatIds = $screening->bookings
            ->flatMap(fn ($booking) => $booking->bookedSeats->pluck('seat_id'));

        return new SeatMap(
            $screening->hall->seats->map(fn (Seat $seat) => new SeatView(
                id: $seat->getKey(),
                row: $seat->row_label,
                seatNumber: $seat->seat_number,
                seatType: $seat->seat_type,
                posX: $seat->pos_x,
                posY: $seat->pos_y,
                isActive: $seat->is_active,
                isBooked: $bookedSeatIds->contains($seat->getKey()),
            ))
        );

        //        $screening = Screening::query()
        //            ->select(['id', 'hall_id'])
        //            ->with([
        //                'hall:id',
        //                'hall.seats:id,hall_id,row_label,seat_number,seat_type,pos_x,pos_y,is_active',
        //                'bookings' => fn ($query) => $query
        //                    ->select(['id', 'screening_id', 'status'])
        //                    ->whereIn('status', [
        //                        BookingStatus::PENDING,
        //                        BookingStatus::CONFIRMED,
        //                    ]),
        //                'bookings.bookedSeats:id,booking_id,seat_id',
        //            ])
        //            ->findOrFail($screeningId);
        //
        //        $bookedSeatIds = $screening->bookings()
        //            ->flatMap(fn ($booking) => $booking->bookedSeats)
        //            ->pluck('seat_id')
        //            ->unique()
        //            ->all();
        //
        //        return new SeatMap(
        //            $screening->hall->seats->map(
        //                fn ($seat): SeatView => new SeatView(
        //                    id: $seat->getKey(),
        //                    row: $seat->row_label,
        //                    seatNumber: $seat->seat_number,
        //                    seatType: $seat->seat_type,
        //                    posX: $seat->pos_x,
        //                    posY: $seat->pos_y,
        //                    isActive: $seat->is_active,
        //                    isBooked: in_array($seat->getKey(), $bookedSeatIds, true),
        //                ),
        //            ),
        //        );
    }
}
