<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case PAY_U = 'payu';
    case PRZELEWY24 = 'przelewy24';
    case CART = 'cart';

    public function label(): string
    {
        return match ($this) {
            self::PAY_U => 'PayU',
            self::PRZELEWY24 => 'Przelewy24',
            self::CART => 'Karta',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PAY_U => 'Szybka płatność online z testowym przekierowaniem.',
            self::PRZELEWY24 => 'Symulacja płatności błyskawicznej bez integracji.',
            self::CART => 'Testowa karta i finalizacja jednym kliknięciem.',
        };
    }
}
