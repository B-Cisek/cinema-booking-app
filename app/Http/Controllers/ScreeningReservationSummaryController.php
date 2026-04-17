<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ScreeningReservationSummaryRequest;
use App\Models\Screening;
use App\Support\Identity\GuestTokenManager;
use App\ViewData\ReservationSummaryPageData;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationSummaryController extends Controller
{
    public function __construct(
        private readonly GuestTokenManager $guestTokenHandler,
        private readonly ReservationSummaryPageData $data,
    ) {}

    public function __invoke(
        ScreeningReservationSummaryRequest $request,
        Screening $screening,
    ): Response {
        $this->guestTokenHandler->setup($request);

        return Inertia::render('ReservationSummary', $this->data->build(
            $screening,
            $request->validated('seatIds')
        ));
    }
}
