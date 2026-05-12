<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Commands\SelectCinema;
use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
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

class ReservationPaymentPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_test_payment_page_for_the_selected_gateway(): void
    {
        [$cinema, $screening, $booking] = $this->prepareBooking();

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->get(route('screenings.reservation-payment', [
                'screening' => $screening,
                'booking' => $booking,
                'paymentMethod' => 'payu',
            ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('ReservationPayment')
                ->where('booking.id', $booking->getKey())
                ->where('booking.number', $booking->booking_number)
                ->where('booking.total', 5600)
                ->where('paymentMethod.code', 'payu')
                ->where('paymentMethod.label', 'PayU')
                ->where('screening.id', $screening->getKey())
            );
    }

    /**
     * @return array{0: Cinema, 1: Screening, 2: Booking}
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
            'status' => BookingStatus::PENDING,
            'customer_email' => 'jan@example.com',
            'payment_method' => PaymentMethod::PAY_U,
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

        return [$cinema, $screening, $booking];
    }
}
