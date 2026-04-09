<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RowLabel;
use App\Enums\ScreeningStatus;
use App\Enums\SeatType;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Seat;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationSummaryPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_reservation_summary_page_for_selected_seats(): void
    {
        $cinema = Cinema::factory()->create([
            'city' => 'Warszawa',
            'street' => 'ul. Marszałkowska 12',
        ]);

        $hall = Hall::query()->create([
            'cinema_id' => $cinema->getKey(),
            'name' => 'main',
            'label' => 'Sala 2',
        ]);

        $movie = Movie::query()->create([
            'title' => 'Interstellar',
            'description' => 'Space drama',
            'duration' => 169,
            'poster_url' => 'https://example.com/interstellar.jpg',
            'is_active' => true,
        ]);

        $startsAt = CarbonImmutable::parse('2026-04-07 20:00:00');

        $screening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($movie->duration),
        ]);

        $firstSeat = Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::B,
            'seat_number' => 4,
            'seat_type' => SeatType::STANDARD,
            'pos_x' => 4,
            'pos_y' => 2,
            'is_active' => true,
        ]);

        $secondSeat = Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::B,
            'seat_number' => 5,
            'seat_type' => SeatType::VIP,
            'pos_x' => 5,
            'pos_y' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('screenings.reservation-summary', [
            'screening' => $screening,
            'seatIds' => [$firstSeat->getKey(), $secondSeat->getKey()],
        ]));

        $response
            ->assertOk()
            ->assertCookie('guest-token')
            ->assertInertia(fn (Assert $page) => $page
                ->component('ReservationSummary')
                ->where('screening.id', $screening->getKey())
                ->where('screening.movie.title', 'Interstellar')
                ->where('screening.hall.label', 'Sala 2')
                ->has('selectedSeats', 2)
                ->where('selectedSeats.0.label', 'B4')
                ->where('selectedSeats.0.seatType', 'standard')
                ->where('selectedSeats.1.label', 'B5')
                ->where('selectedSeats.1.seatType', 'vip')
            );
    }

    #[Test]
    public function it_redirects_back_to_the_reservation_page_when_selected_seats_do_not_belong_to_the_screening(): void
    {
        $cinema = Cinema::factory()->create();

        $hall = Hall::query()->create([
            'cinema_id' => $cinema->getKey(),
            'name' => 'main',
            'label' => 'Sala 1',
        ]);

        $anotherHall = Hall::query()->create([
            'cinema_id' => $cinema->getKey(),
            'name' => 'secondary',
            'label' => 'Sala 2',
        ]);

        $movie = Movie::query()->create([
            'title' => 'Blade Runner 2049',
            'description' => 'Neo-noir sci-fi',
            'duration' => 164,
            'poster_url' => 'https://example.com/bladerunner.jpg',
            'is_active' => true,
        ]);

        $startsAt = CarbonImmutable::parse('2026-04-07 21:00:00');

        $screening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($movie->duration),
        ]);

        $foreignSeat = Seat::query()->create([
            'hall_id' => $anotherHall->getKey(),
            'row_label' => RowLabel::C,
            'seat_number' => 7,
            'seat_type' => SeatType::STANDARD,
            'pos_x' => 7,
            'pos_y' => 3,
            'is_active' => true,
        ]);

        $response = $this->get(route('screenings.reservation-summary', [
            'screening' => $screening,
            'seatIds' => [$foreignSeat->getKey()],
        ]));

        $response->assertRedirect(route('screenings.reservation', $screening));
    }
}
