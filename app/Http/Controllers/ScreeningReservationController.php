<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Support\Identity\GuestTokenManager;
use App\ViewData\ReservationPageData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationController extends Controller
{
    public function __construct(
        private readonly GuestTokenManager $guestTokenHandler,
        private readonly ReservationPageData $data
    ) {}

    public function __invoke(Request $request, Screening $screening): Response
    {
        $this->guestTokenHandler->setup($request);

        return Inertia::render('Reservation', $this->data->build($screening));
    }
}
