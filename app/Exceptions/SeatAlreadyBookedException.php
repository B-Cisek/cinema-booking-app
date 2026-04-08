<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ResponseCode;
use App\Services\JsonResponseFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SeatAlreadyBookedException extends Exception
{
    public function report(): void
    {
        Log::notice('Seat already booked');
    }

    public function render(): JsonResponse
    {
        return new JsonResponseFactory()->make(ResponseCode::SEAT_ALREADY_BOOKED);
    }
}
