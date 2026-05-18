<?php

declare(strict_types=1);

namespace App\Support\Payment;

interface PaymentGateway
{
    public function start(Payment $payment): PaymentRedirect;
}
