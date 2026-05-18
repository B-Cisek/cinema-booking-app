<?php

declare(strict_types=1);

namespace App\Support\Payment;

use App\Models\Booking;

readonly class Payment
{
    public function __construct(
        public Booking $booking,
        public string $customerIp
    ) {}
}
