<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BookedSeat;
use App\Models\Booking;
use App\Models\Screening;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationSuccessController extends Controller
{
    public function __invoke(Screening $screening, Booking $booking): Response
    {
        $booking->loadMissing([
            'screening.hall.cinema',
            'screening.movie',
            'bookedSeats.seat',
        ]);

        if ($booking->screening_id !== $screening->getKey()) {
            throw new ModelNotFoundException;
        }

        return Inertia::render('ReservationSuccess', [
            'booking' => [
                'id' => $booking->getKey(),
                'number' => $booking->booking_number,
                'email' => $booking->customer_email,
                'total' => $booking->bookedSeats->sum('price'),
                'seats' => $booking->bookedSeats
                    ->map(fn (BookedSeat $bookedSeat): array => [
                        'id' => $bookedSeat->getKey(),
                        'label' => sprintf(
                            '%s%s',
                            $bookedSeat->seat->row_label->value,
                            $bookedSeat->seat->seat_number,
                        ),
                        'price' => $bookedSeat->price,
                    ])
                    ->values()
                    ->all(),
            ],
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $booking->screening->starts_at->format('H:i'),
                'ends_at' => $booking->screening->ends_at->format('H:i'),
                'date' => $booking->screening->starts_at->locale('pl')->translatedFormat('j F Y'),
                'hall' => [
                    'label' => $booking->screening->hall->label,
                    'cinema' => [
                        'city' => $booking->screening->hall->cinema->city,
                        'street' => $booking->screening->hall->cinema->street,
                    ],
                ],
                'movie' => [
                    'title' => $booking->screening->movie->title,
                    'poster_url' => $booking->screening->movie->poster_url,
                ],
            ],
        ]);
    }
}
