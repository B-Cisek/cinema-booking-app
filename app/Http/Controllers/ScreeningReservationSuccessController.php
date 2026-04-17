<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Screening;
use App\ViewData\ReservationSuccessPageData;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationSuccessController extends Controller
{
    public function __construct(private readonly ReservationSuccessPageData $data) {}

    public function __invoke(Screening $screening, Booking $booking): Response
    {
        return Inertia::render('ReservationSuccess', $this->data->build($booking, $screening));
    }
}
