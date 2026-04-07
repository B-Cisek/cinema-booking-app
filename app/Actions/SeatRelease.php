<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\CinemaNotSelectException;
use App\Services\CinemaResolver;
use App\Services\GuestTokenHandler;
use App\Services\SeatHoldService;
use Illuminate\Http\Request;

readonly class SeatRelease
{
    public function __construct(
        private SeatHoldService $seatHoldService,
        private CinemaResolver $cinemaResolver,
        private GuestTokenHandler $guestTokenHandler,
    ) {}

    public function handle(Request $request): void
    {
        $cinema = $this->cinemaResolver->resolve($request);

        if (! $cinema) {
            throw new CinemaNotSelectException;
        }

        $this->seatHoldService->release(
            cinemaId: $cinema->getKey(),
            screeningId: $request->input('screeningId'),
            seatId: $request->input('seatId'),
            ownerIdentifier: $this->guestTokenHandler->resolve($request),
        );
    }
}
