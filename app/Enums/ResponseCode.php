<?php

declare(strict_types=1);

namespace App\Enums;

enum ResponseCode: string
{
    case SEAT_ALREADY_RESERVED = 'SEAT_ALREADY_RESERVED';
    case SEAT_ALREADY_BOOKED = 'SEAT_ALREADY_BOOKED';

    case SEAT_HELD = 'SEAT_HELD';

    case CINEMA_NOT_SELECTED = 'CINEMA_NOT_SELECTED';

    case SEAT_RELEASED = 'SEAT_RELEASED';

    public function message(): string
    {
        return __(sprintf('response.%s', $this->value));
    }

    public function status(): int
    {
        return match ($this) {
            self::SEAT_ALREADY_RESERVED, self::SEAT_ALREADY_BOOKED => 409,
            self::SEAT_HELD, self::SEAT_RELEASED => 200,
            default => 400,
        };
    }
}
