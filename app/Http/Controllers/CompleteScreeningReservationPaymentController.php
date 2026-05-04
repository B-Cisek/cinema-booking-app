<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\ConfirmScreeningReservationPayment;
use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Screening;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

class CompleteScreeningReservationPaymentController extends Controller
{
    public function __construct(
        private readonly ConfirmScreeningReservationPayment $confirmScreeningReservationPayment,
    ) {}

    public function __invoke(
        Screening $screening,
        Booking $booking,
        string $paymentMethod,
    ): RedirectResponse {
        if (PaymentMethod::tryFrom($paymentMethod) === null) {
            throw new ModelNotFoundException;
        }

        if ($booking->screening_id !== $screening->getKey()) {
            throw new ModelNotFoundException;
        }

        $this->confirmScreeningReservationPayment->handle($booking);

        return redirect()->route('screenings.reservation-success', [
            'screening' => $screening,
            'booking' => $booking,
        ]);
    }
}
