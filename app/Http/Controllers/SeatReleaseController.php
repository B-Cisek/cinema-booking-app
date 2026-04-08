<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SeatRelease;
use App\Enums\ResponseCode;
use App\Http\Requests\SeatReleaseRequest;
use App\Services\JsonResponseFactory;
use Illuminate\Http\JsonResponse;

class SeatReleaseController extends Controller
{
    public function __construct(private readonly SeatRelease $seatRelease) {}

    public function __invoke(SeatReleaseRequest $request): JsonResponse
    {
        $this->seatRelease->handle($request);

        return JsonResponseFactory::make(ResponseCode::SEAT_RELEASED);
    }
}
