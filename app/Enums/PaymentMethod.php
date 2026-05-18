<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case PAY_U = 'payu';

    public function label(): string
    {
        return match ($this) {
            self::PAY_U => 'PayU',
        };
    }
}
