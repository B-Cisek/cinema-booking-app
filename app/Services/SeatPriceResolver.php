<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Seat;

interface SeatPriceResolver
{
    public function forSeat(Seat $seat): int;
}
