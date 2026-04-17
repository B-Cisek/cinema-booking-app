<?php

declare(strict_types=1);

namespace App\ViewData;

use App\Models\Screening;
use App\Models\Seat;
use App\Support\Pricing\SeatPriceCalculator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

readonly class ReservationSummaryPageData
{
    public function __construct(private SeatPriceCalculator $seatPriceCalculator) {}

    public function build(Screening $screening, array $selectedSeatIds): array
    {
        $screening->loadMissing(['hall.cinema', 'movie']);

        $selectedSeats = $screening->hall->seats()
            ->whereIn('id', $selectedSeatIds)
            ->where('is_active', true)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        if ($selectedSeats->count() !== count($selectedSeatIds)) {
            Inertia::flash('error', __('screening.reservation_summary.error_message'));

            Log::critical('SELECTED_SEATS_COUNT_MISMATCH', [
                'selectedSeat' => $selectedSeatIds,
                'selectedSeatsFromDB' => $selectedSeats->pluck('id')->toArray(),
            ]);

            throw new HttpResponseException(
                redirect()->route('screenings.reservation', $screening)
            );
        }

        $selectedSeatPayload = $selectedSeats->map(
            fn (Seat $seat): array => [
                'id' => $seat->id,
                'label' => sprintf('%s%s', $seat->row_label->value, $seat->seat_number),
                'row' => $seat->row_label->value,
                'seatNumber' => $seat->seat_number,
                'seatType' => $seat->seat_type->value,
                'price' => $this->seatPriceCalculator->forSeat($seat),
            ],
        )->values();

        return [
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $screening->starts_at->format('H:i'),
                'ends_at' => $screening->ends_at->format('H:i'),
                'date' => $screening->starts_at->locale(App::currentLocale())->translatedFormat('j F Y'),
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
        ];
    }
}
