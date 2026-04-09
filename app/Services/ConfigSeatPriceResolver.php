<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Seat;
use RuntimeException;

class ConfigSeatPriceResolver implements SeatPriceResolver
{
    public function forSeat(Seat $seat): int
    {
        $price = config(sprintf('seat.prices.%s', $seat->seat_type->value));

        if (! is_int($price)) {
            throw new RuntimeException(
                sprintf('Missing seat price configuration for seat type [%s].', $seat->seat_type->value),
            );
        }

        return $price;
    }
}
