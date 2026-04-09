<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ScreeningReservationSummaryRequest;
use App\Models\Screening;
use App\Models\Seat;
use App\Services\GuestTokenHandler;
use App\Services\SeatPriceCalculator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationSummaryController extends Controller
{
    public function __construct(
        private readonly GuestTokenHandler $guestTokenHandler,
        private readonly SeatPriceCalculator $seatPriceCalculator,
    ) {}

    public function __invoke(
        ScreeningReservationSummaryRequest $request,
        Screening $screening,
    ): Response {
        $this->guestTokenHandler->setup($request);
        $screening->loadMissing(['hall.cinema', 'movie']);

        /** @var array<int, string> $selectedSeatIds */
        $selectedSeatIds = $request->validated('seatIds');
        $selectedSeats = $screening->hall->seats()
            ->whereIn('id', $selectedSeatIds)
            ->where('is_active', true)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        if ($selectedSeats->count() !== count($selectedSeatIds)) {
            throw new HttpResponseException(
                redirect()->route('screenings.reservation', $screening)
            );
        }

        $selectedSeatPayload = $selectedSeats->map(
            fn (Seat $seat): array => [
                'id' => $seat->getKey(),
                'label' => sprintf('%s%s', $seat->row_label->value, $seat->seat_number),
                'row' => $seat->row_label->value,
                'seatNumber' => $seat->seat_number,
                'seatType' => $seat->seat_type->value,
                'price' => $this->seatPriceCalculator->forSeat($seat),
            ],
        )->values();

        return Inertia::render('ReservationSummary', [
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $screening->starts_at->format('H:i'),
                'ends_at' => $screening->ends_at->format('H:i'),
                'date' => $screening->starts_at->locale('pl')->translatedFormat('j F Y'),
                'hall' => [
                    'label' => $screening->hall->label,
                    'cinema' => [
                        'city' => $screening->hall->cinema->city,
                        'street' => $screening->hall->cinema->street,
                    ],
                ],
                'movie' => [
                    'title' => $screening->movie->title,
                    'description' => $screening->movie->description,
                    'duration' => $screening->movie->duration,
                    'poster_url' => $screening->movie->poster_url,
                ],
            ],
            'selectedSeats' => $selectedSeatPayload->all(),
            'totalPrice' => $selectedSeatPayload->sum('price'),
        ]);
    }
}
