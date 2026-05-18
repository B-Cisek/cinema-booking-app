<?php

declare(strict_types=1);

namespace App\Support\Payment;

readonly class PaymentRedirect
{
    public function __construct(public string $url) {}
}
