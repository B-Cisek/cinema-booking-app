<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SeatHold;
use App\Enums\ResponseCode;
use App\Http\Requests\SeatHoldRequest;
use App\Services\JsonResponseFactory;
use Illuminate\Http\JsonResponse;

class SeatHoldController extends Controller
{
    public function __construct(private readonly SeatHold $seatHold) {}

    public function __invoke(SeatHoldRequest $request): JsonResponse
    {
        $this->seatHold->handle($request);

        return JsonResponseFactory::make(ResponseCode::SEAT_HELD);
    }
}
