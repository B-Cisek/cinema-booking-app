<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CinemaNotSelectException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::error('Cinema not selected');
    }
}
