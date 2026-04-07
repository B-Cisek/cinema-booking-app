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

class ReservationPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_reservation_page_with_the_seat_layout_needed_by_the_frontend(): void
    {
        $cinema = Cinema::factory()->create([
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

        $startsAt = CarbonImmutable::parse('2026-04-07 18:00:00');

        $screening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addMinutes($movie->duration),
        ]);

        Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::A,
            'seat_number' => 1,
            'seat_type' => SeatType::STANDARD,
            'pos_x' => 1,
            'pos_y' => 1,
            'is_active' => true,
        ]);

        Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::A,
            'seat_number' => 2,
            'seat_type' => SeatType::VIP,
            'pos_x' => 2,
            'pos_y' => 1,
            'is_active' => false,
        ]);

        $response = $this->get(route('screenings.reservation', $screening));

        $response
            ->assertOk()
            ->assertCookie('guest-token')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Reservation')
                ->where('screening.id', $screening->getKey())
                ->where('screening.movie.title', 'Diuna')
                ->where('screening.hall.label', 'Sala 1')
                ->has('seats', 1, fn (Assert $row) => $row
                    ->where('label', 'A')
                    ->has('seats', 25)
                    ->where('seats.0.row', 'A')
                    ->where('seats.0.seatNumber', 1)
                    ->where('seats.0.seatType', 'standard')
                    ->where('seats.0.isActive', true)
                    ->where('seats.0.isBooked', false)
                    ->where('seats.1.row', 'A')
                    ->where('seats.1.seatNumber', 2)
                    ->where('seats.1.seatType', 'vip')
                    ->where('seats.1.isActive', false)
                    ->where('seats.1.isBooked', false)
                    ->etc()
                )
            );
    }
}
