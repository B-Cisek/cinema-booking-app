<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Oczekuje',
            self::CONFIRMED => 'Opłacona',
            self::CANCELLED => 'Anulowana',
        };
    }
}
