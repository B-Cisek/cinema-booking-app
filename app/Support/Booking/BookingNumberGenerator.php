<?php

declare(strict_types=1);

namespace App\Support\Booking;

use Random\Randomizer;

readonly class BookingNumberGenerator
{
    private const string PREFIX = 'BK';

    private const string ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    private const int LENGTH = 6;

    public function generate(): string
    {
        $randomizer = new Randomizer;
        $maxIndex = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[$randomizer->getInt(0, $maxIndex)];
        }

        return self::PREFIX.$code;
    }
}
