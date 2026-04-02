<?php

namespace Tests\Feature;

use App\Enums\ScreeningStatus;
use App\Exceptions\CinemaNotFoundException;
use App\Http\Controllers\SelectCinemaController;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SelectedCinemaTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_selected_cinema_id_in_the_session(): void
    {
        $cinema = Cinema::query()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Zlota 11',
        ]);

        $response = $this->post(route('cinemas.select'), [
            'id' => $cinema->getKey(),
        ]);

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHas(
                SelectCinemaController::CINEMA_SESSION_KEY,
                $cinema->getKey(),
            )
            ->assertCookie(config('app.selected_cinema_cookie_name'), $cinema->getKey());
    }

    #[Test]
    public function it_throw_exception_when_cinema_is_not_exist(): void
    {
        $this->withoutExceptionHandling();

        $this->assertThrows(
            fn () => $this->post(route('cinemas.select'), [
                'id' => Uuid::uuid7()->toString(),
            ]),
            fn (CinemaNotFoundException $exception) => $exception->getMessage() === 'Invalid cinema',
        );
    }

    #[Test]
    public function it_shows_screenings_for_the_selected_cinema_on_home_page(): void
    {
        $selectedCinema = Cinema::query()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Zlota 11',
        ]);

        $otherCinema = Cinema::query()->create([
            'city' => 'Krakow',
            'street' => 'ul. Dluga 5',
        ]);

        $selectedHall = Hall::query()->create([
            'cinema_id' => $selectedCinema->getKey(),
            'name' => 'main',
            'label' => 'Sala 1',
        ]);

        $otherHall = Hall::query()->create([
            'cinema_id' => $otherCinema->getKey(),
            'name' => 'side',
            'label' => 'Sala 2',
        ]);

        $movie = Movie::query()->create([
            'title' => 'Diuna',
            'description' => 'Sci-fi epic',
            'duration' => 155,
            'poster_url' => 'https://example.com/dune.jpg',
            'is_active' => true,
        ]);

        $startsAt = CarbonImmutable::now()->startOfDay()->addHours(18);

        $selectedScreening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $selectedHall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($movie->duration),
        ]);

        Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $otherHall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($movie->duration),
        ]);

        $response = $this
            ->withSession([
                SelectCinemaController::CINEMA_SESSION_KEY => $selectedCinema->getKey(),
            ])
            ->get(route('home'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('scheduleDays')
            ->has('screenings', 1, fn (Assert $screening) => $screening
                ->where('id', $selectedScreening->getKey())
                ->where('date', $startsAt->toDateString())
                ->where('starts_at', $startsAt->format('H:i'))
                ->where('ends_at', $startsAt->addMinutes($movie->duration)->format('H:i'))
                ->where('status', ScreeningStatus::SCHEDULED->value)
                ->where('hall.label', 'Sala 1')
                ->where('movie.title', 'Diuna')
                ->where('movie.description', 'Sci-fi epic')
                ->where('movie.duration', 155)
                ->where('movie.poster_url', 'https://example.com/dune.jpg')
                ->etc()
            )
        );
    }

    #[Test]
    public function it_shows_only_screenings_from_the_home_schedule_range(): void
    {
        $cinema = Cinema::query()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Zlota 11',
        ]);

        $hall = Hall::query()->create([
            'cinema_id' => $cinema->getKey(),
            'name' => 'main',
            'label' => 'Sala 1',
        ]);

        $movie = Movie::query()->create([
            'title' => 'Diuna',
            'description' => 'Sci-fi epic',
            'duration' => 155,
            'poster_url' => 'https://example.com/dune.jpg',
            'is_active' => true,
        ]);

        $inRangeStartsAt = CarbonImmutable::now()->startOfDay()->addDays(6)->addHours(18);
        $outOfRangeStartsAt = CarbonImmutable::now()->startOfDay()->addDays(7)->addHours(18);

        $inRangeScreening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $inRangeStartsAt,
            'ends_at' => $inRangeStartsAt->addMinutes($movie->duration),
        ]);

        Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $outOfRangeStartsAt,
            'ends_at' => $outOfRangeStartsAt->addMinutes($movie->duration),
        ]);

        $response = $this
            ->withSession([
                SelectCinemaController::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->get(route('home'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('screenings', 1, fn (Assert $screening) => $screening
                ->where('id', $inRangeScreening->getKey())
                ->where('date', $inRangeStartsAt->toDateString())
                ->etc()
            )
        );
    }

    #[Test]
    public function it_restores_the_selected_cinema_from_cookie_when_session_is_missing(): void
    {
        $cinema = Cinema::query()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Zlota 11',
        ]);

        $response = $this
            ->withCookie(config('app.selected_cinema_cookie_name'), $cinema->getKey())
            ->get(route('home'));

        $response
            ->assertOk()
            ->assertSessionHas(SelectCinemaController::CINEMA_SESSION_KEY, $cinema->getKey())
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->where('selectedCinema.id', $cinema->getKey())
                ->where('selectedCinema.city', 'Warszawa')
                ->where('selectedCinema.street', 'ul. Zlota 11')
            );
    }
}
