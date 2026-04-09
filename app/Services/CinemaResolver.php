<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\SelectCinema;
use App\Models\Cinema;
use App\Repositories\CinemaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

readonly class CinemaResolver
{
    private const string REQUEST_ATTRIBUTE_KEY = 'selected_cinema';

    public function __construct(private CinemaRepository $cinemaRepository) {}

    public function resolve(Request $request): ?Cinema
    {
        if ($request->attributes->has(self::REQUEST_ATTRIBUTE_KEY)) {
            /** @var Cinema|null $selectedCinema */
            $selectedCinema = $request->attributes->get(self::REQUEST_ATTRIBUTE_KEY);

            return $selectedCinema;
        }

        $selectedCinemaId = $request->session()->get(SelectCinema::CINEMA_SESSION_KEY);

        if ($selectedCinemaId) {
            $selectedCinema = $this->cinemaRepository->getById($selectedCinemaId);

            if ($selectedCinema !== null) {
                $request->attributes->set(self::REQUEST_ATTRIBUTE_KEY, $selectedCinema);

                return $selectedCinema;
            }

            $request->session()->forget(SelectCinema::CINEMA_SESSION_KEY);
        }

        $selectedCinemaIdFromCookie = $request->cookie($this->cookieName());

        if (! is_string($selectedCinemaIdFromCookie) || $selectedCinemaIdFromCookie === '') {
            $request->attributes->set(self::REQUEST_ATTRIBUTE_KEY, null);

            return null;
        }

        $selectedCinema = $this->cinemaRepository->getById($selectedCinemaIdFromCookie);

        if ($selectedCinema === null) {
            Cookie::expire($this->cookieName());
            $request->attributes->set(self::REQUEST_ATTRIBUTE_KEY, null);

            return null;
        }

        $request->session()->put(
            SelectCinema::CINEMA_SESSION_KEY,
            $selectedCinema->getKey(),
        );
        $request->attributes->set(self::REQUEST_ATTRIBUTE_KEY, $selectedCinema);

        return $selectedCinema;
    }

    public function queueCookie(string $cinemaId): void
    {
        Cookie::queue(
            $this->cookieName(),
            $cinemaId,
            $this->cookieLifetimeInMinutes()
        );
    }

    private function cookieName(): string
    {
        return Config::string('app.selected_cinema_cookie_name');
    }

    private function cookieLifetimeInMinutes(): int
    {
        return Config::integer('app.selected_cinema_cookie_lifetime_minutes');
    }
}
