<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SeatAlreadyBookedException extends Exception
{
    public function report(): void
    {
        Log::notice('Seat already booked');
    }
}
