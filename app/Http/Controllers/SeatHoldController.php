<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SeatHold;
use App\Http\Requests\SeatHoldRequest;
use App\Services\CinemaResolver;
use App\Services\GuestTokenHandler;
use App\Services\SeatHoldService;
use Illuminate\Http\JsonResponse;

class SeatHoldController extends Controller
{
    public function __construct(
        private readonly SeatHoldService $seatHoldService,
        private readonly CinemaResolver $cinemaResolver,
        private readonly GuestTokenHandler $guestTokenHandler,
        private readonly SeatHold $seatHold
    ) {}

    public function __invoke(SeatHoldRequest $request): JsonResponse
    {
        $this->seatHold->handle($request);

        return new JsonResponse([
            'message' => 'Seat hold successful',
        ]);

        $cinema = $this->cinemaResolver->resolve($request);

        if (! $cinema) {
            return new JsonResponse(['message' => 'Cinema not found'], 404);
        }

        $this->seatHoldService->hold(
            cinemaId: $cinema->getKey(),
            screeningId: $request->input('screeningId'),
            seatId: $request->input('seatId'),
            ownerIdentifier: $this->guestTokenHandler->resolve($request)
        );

    }
}
