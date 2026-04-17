<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\SeatAlreadyBookedException;
use App\Exceptions\SeatAlreadyReservedException;
use App\Repositories\BookingRepository;
use App\Support\Seats\SeatHoldService;
use Illuminate\Support\Facades\Log;

readonly class SeatHold
{
    public function __construct(
        private SeatHoldService $seatHoldService,
        private BookingRepository $bookingRepository,
    ) {}

    public function handle(string $screeningId, string $seatId, string $cinemaId, string $ownerIdentifier): void
    {
        $isBooked = $this->bookingRepository->isSeatBooked($screeningId, $seatId);

        if ($isBooked) {
            throw new SeatAlreadyBookedException;
        }

        $expiresAt = $this->seatHoldService->hold(
            cinemaId: $cinemaId,
            screeningId: $screeningId,
            seatId: $seatId,
            ownerIdentifier: $ownerIdentifier,
        );

        if ($expiresAt === null) {
            throw new SeatAlreadyReservedException;
        }

        Log::debug('SEAT_HOLD', [
            'expires_at' => $expiresAt,
            'cinemaId' => $cinemaId,
            'screeningId' => $screeningId,
            'seatId' => $seatId,
            'ownerIdentifier' => $ownerIdentifier,
        ]);
    }
}
