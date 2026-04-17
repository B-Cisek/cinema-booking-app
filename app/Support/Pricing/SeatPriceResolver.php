<?php

declare(strict_types=1);

namespace App\Support\Pricing;

use App\Models\Seat;

interface SeatPriceResolver
{
    public function forSeat(Seat $seat): int;
}
