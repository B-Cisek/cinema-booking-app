<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Screening;
use App\Support\Payment\Payment;
use App\Support\Payment\PaymentGateway;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScreeningReservationPaymentController extends Controller
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway,
    ) {}

    public function __invoke(Screening $screening, Booking $booking, Request $request)
    {
        abort_unless($booking->screening_id === $screening->getKey(), 404);

        $result = $this->paymentGateway->start(new Payment(booking: $booking, customerIp: $request->ip()));

        return Inertia::location($result->url);
    }
}
