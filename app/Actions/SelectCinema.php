<?php

namespace App\Actions;

use App\Exceptions\CinemaNotFoundException;
use App\Repositories\CinemaRepository;
use App\Services\CinemaResolver;
use Illuminate\Http\Request;

readonly class SelectCinema
{
    public const string CINEMA_SESSION_KEY = 'cinema_id';

    public function __construct(
        private CinemaRepository $cinemaRepository,
        private CinemaResolver $cinemaResolver,
    ) {}

    /**
     * @throws \Throwable
     */
    public function handle(Request $request, string $cinemaId): void
    {
        throw_unless(
            condition: $this->cinemaRepository->isExist($cinemaId),
            exception: CinemaNotFoundException::class,
        );

        $request->session()->put(self::CINEMA_SESSION_KEY, $cinemaId);
        $this->cinemaResolver->queueCookie($cinemaId);
    }
}
