<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Seat;

readonly class SeatPriceCalculator
{
    public function __construct(
        private SeatPriceResolver $seatPriceResolver,
    ) {}

    public function forSeat(Seat $seat): int
    {
        return $this->seatPriceResolver->forSeat($seat);
    }
}
