<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\CreateReservationHandler;
use App\Enums\PaymentMethod;
use App\Http\Requests\CreateReservationRequest;
use App\Models\Screening;
use App\Support\Identity\GuestTokenManager;
use Illuminate\Http\RedirectResponse;

class StoreScreeningReservationController extends Controller
{
    public function __construct(
        private readonly CreateReservationHandler $createReservation,
        private readonly GuestTokenManager $guestTokenHandler,
    ) {}

    public function __invoke(CreateReservationRequest $request, Screening $screening): RedirectResponse
    {
        $paymentMethod = PaymentMethod::from($request->validated('paymentMethod'));
        $authUser = $request->user();

        $userIdentifier = $authUser === null
            ? $this->guestTokenHandler->resolve($request)
            : $authUser->id;

        $booking = $this->createReservation->handle(
            screening: $screening,
            seatIds: $request->validated('seatIds'),
            userIdentifier: $userIdentifier,
            paymentMethod: $paymentMethod,
            customerEmail: $request->validated('email'),
        );

        return redirect()->route('screenings.reservation-payment', [
            'screening' => $screening,
            'booking' => $booking,
            'paymentMethod' => $paymentMethod->value,
        ]);
    }
}
