<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\CinemaNotSelectException;
use App\Exceptions\SeatAlreadyBookedException;
use App\Exceptions\SeatAlreadyReservedException;
use App\Repositories\BookingRepository;
use App\Services\CinemaResolver;
use App\Services\GuestTokenHandler;
use App\Services\SeatHoldService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

readonly class SeatHold
{
    public function __construct(
        private CinemaResolver $cinemaResolver,
        private SeatHoldService $seatHoldService,
        private GuestTokenHandler $guestTokenHandler,
        private BookingRepository $bookingRepository,
    ) {}

    public function handle(Request $request): void
    {
        $cinema = $this->cinemaResolver->resolve($request);
        $screeningId = $request->input('screeningId');
        $seatId = $request->input('seatId');
        $ownerIdentifier = $this->guestTokenHandler->resolve($request);

        if ($cinema === null) {
            throw new CinemaNotSelectException;
        }

        $isBooked = $this->bookingRepository->isSeatBooked($screeningId, $seatId);

        if ($isBooked) {
            throw new SeatAlreadyBookedException;
        }

        $expiresAt = $this->seatHoldService->hold(
            cinemaId: $cinema->getKey(),
            screeningId: $screeningId,
            seatId: $seatId,
            ownerIdentifier: $ownerIdentifier,
        );

        if ($expiresAt === null) {
            throw new SeatAlreadyReservedException;
        }

        Log::debug('SEAT_HOLD', [
            'expires_at' => $expiresAt,
            'cinemaId' => $cinema->getKey(),
            'screeningId' => $screeningId,
            'seatId' => $seatId,
            'ownerIdentifier' => $ownerIdentifier,
        ]);
    }
}
