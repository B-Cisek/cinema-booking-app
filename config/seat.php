<?php

declare(strict_types=1);

return [
    'prices' => [
        'standard' => (int) env('SEAT_PRICE_STANDARD', 2200),
        'vip' => (int) env('SEAT_PRICE_VIP', 3200),
        'wheelchair' => (int) env('SEAT_PRICE_WHEELCHAIR', 2200),
        'couple' => (int) env('SEAT_PRICE_COUPLE', 4200),
    ],
];
