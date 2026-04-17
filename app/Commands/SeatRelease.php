<?php

declare(strict_types=1);

namespace App\Commands;

use App\Support\Seats\SeatHoldService;

readonly class SeatRelease
{
    public function __construct(
        private SeatHoldService $seatHoldService,
    ) {}

    public function handle(string $screeningId, string $seatId, string $cinemaId, string $ownerIdentifier): void
    {
        $this->seatHoldService->release(
            cinemaId: $cinemaId,
            screeningId: $screeningId,
            seatId: $seatId,
            ownerIdentifier: $ownerIdentifier,
        );
    }
}
