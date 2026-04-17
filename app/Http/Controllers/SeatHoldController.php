<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\SeatHold;
use App\Enums\ResponseCode;
use App\Exceptions\CinemaNotSelectException;
use App\Http\Requests\SeatHoldRequest;
use App\Support\Http\JsonResponseFactory;
use App\Support\Identity\CinemaResolver;
use App\Support\Identity\GuestTokenManager;
use Illuminate\Http\JsonResponse;

class SeatHoldController extends Controller
{
    public function __construct(
        private readonly SeatHold $seatHold,
        private readonly CinemaResolver $cinemaResolver,
        private readonly GuestTokenManager $guestTokenHandler,
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
