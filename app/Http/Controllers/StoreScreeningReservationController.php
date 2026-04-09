<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateScreeningReservation;
use App\Http\Requests\StoreScreeningReservationRequest;
use App\Models\Screening;
use Illuminate\Http\RedirectResponse;

class StoreScreeningReservationController extends Controller
{
    public function __construct(
        private readonly CreateScreeningReservation $createScreeningReservation,
    ) {}

    public function __invoke(
        StoreScreeningReservationRequest $request,
        Screening $screening,
    ): RedirectResponse {
        $booking = $this->createScreeningReservation->handle(
            request: $request,
            screening: $screening,
            customerEmail: $request->validated('email'),
            seatIds: $request->validated('seatIds'),
        );

        return redirect()->route('screenings.reservation-success', [
            'screening' => $screening,
            'booking' => $booking,
        ]);
    }
}
