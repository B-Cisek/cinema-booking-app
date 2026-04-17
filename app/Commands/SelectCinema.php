<?php

declare(strict_types=1);

namespace App\Commands;

use App\Exceptions\CinemaNotFoundException;
use App\Repositories\CinemaRepository;
use App\Support\Identity\CinemaResolver;
use Illuminate\Support\Facades\Session;

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
    public function handle(string $cinemaId): void
    {
        throw_unless(
            condition: $this->cinemaRepository->isExist($cinemaId),
            exception: CinemaNotFoundException::class,
        );

        Session::put(self::CINEMA_SESSION_KEY, $cinemaId);
        $this->cinemaResolver->queueCookie($cinemaId);
    }
}
