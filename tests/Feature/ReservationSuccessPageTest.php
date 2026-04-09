<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\RowLabel;
use App\Enums\ScreeningStatus;
use App\Enums\SeatType;
use App\Models\BookedSeat;
use App\Models\Booking;
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

class ReservationSuccessPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_reservation_success_page(): void
    {
        [$screening, $booking] = $this->prepareBooking();

        $response = $this->get(route('screenings.reservation-success', [
            'screening' => $screening,
            'booking' => $booking,
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('ReservationSuccess')
                ->where('booking.id', $booking->getKey())
                ->where('booking.number', $booking->booking_number)
                ->where('booking.email', 'jan@example.com')
                ->where('booking.total', 5600)
                ->has('booking.seats', 2)
                ->where('booking.seats.0.price', 2200)
                ->where('booking.seats.1.price', 3400)
                ->where('screening.id', $screening->getKey())
                ->where('screening.movie.title', 'Diuna')
            );
    }

    /**
     * @return array{0: Screening, 1: Booking}
     */
    private function prepareBooking(): array
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

        $booking = Booking::query()->create([
            'screening_id' => $screening->getKey(),
            'booking_number' => 'ABC1234567',
            'status' => BookingStatus::CONFIRMED,
            'customer_email' => 'jan@example.com',
        ]);

        $firstSeat = Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::A,
            'seat_number' => 1,
            'seat_type' => SeatType::STANDARD,
            'pos_x' => 1,
            'pos_y' => 1,
            'is_active' => true,
        ]);

        $secondSeat = Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::A,
            'seat_number' => 2,
            'seat_type' => SeatType::VIP,
            'pos_x' => 2,
            'pos_y' => 1,
            'is_active' => true,
        ]);

        BookedSeat::query()->create([
            'booking_id' => $booking->getKey(),
            'seat_id' => $firstSeat->getKey(),
            'price' => 2200,
        ]);

        BookedSeat::query()->create([
            'booking_id' => $booking->getKey(),
            'seat_id' => $secondSeat->getKey(),
            'price' => 3400,
        ]);

        return [$screening, $booking];
    }
}
