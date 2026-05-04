<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Screening;
use App\ViewData\ReservationPaymentPageData;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationPaymentController extends Controller
{
    public function __construct(private readonly ReservationPaymentPageData $data) {}

    public function __invoke(
        Screening $screening,
        Booking $booking,
        string $paymentMethod,
    ): Response {
        $resolvedPaymentMethod = PaymentMethod::tryFrom($paymentMethod);

        if ($resolvedPaymentMethod === null) {
            throw new ModelNotFoundException;
        }

        return Inertia::render('ReservationPayment', $this->data->build(
            $booking,
            $screening,
            $resolvedPaymentMethod,
        ));
    }
}
