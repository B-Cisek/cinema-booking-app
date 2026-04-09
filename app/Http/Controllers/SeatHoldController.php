<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SeatHold;
use App\Enums\ResponseCode;
use App\Exceptions\CinemaNotSelectException;
use App\Http\Requests\SeatHoldRequest;
use App\Services\CinemaResolver;
use App\Services\GuestTokenHandler;
use App\Services\JsonResponseFactory;
use Illuminate\Http\JsonResponse;

class SeatHoldController extends Controller
{
    public function __construct(
        private readonly SeatHold $seatHold,
        private readonly CinemaResolver $cinemaResolver,
        private readonly GuestTokenHandler $guestTokenHandler,
    ) {}

    public function __invoke(SeatHoldRequest $request): JsonResponse
    {
        $cinema = $this->cinemaResolver->resolve($request);
        $ownerIdentifier = $this->guestTokenHandler->resolve($request);

        if ($cinema === null) {
            throw new CinemaNotSelectException;
        }

        $this->seatHold->handle(
            screeningId: $request->validated('screeningId'),
            seatId: $request->validated('seatId'),
            cinemaId: $cinema->getKey(),
            ownerIdentifier: $ownerIdentifier,
        );

        return JsonResponseFactory::make(ResponseCode::SEAT_HELD);
    }
}
