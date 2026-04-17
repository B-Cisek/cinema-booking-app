<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ResponseCode;
use App\Support\Http\JsonResponseFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CinemaNotSelectException extends Exception
{
    public function report(): void
    {
        Log::info('Cinema not selected');
    }

    public function render(): JsonResponse
    {
        return JsonResponseFactory::make(ResponseCode::CINEMA_NOT_SELECTED);
    }
}
