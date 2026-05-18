<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\RowLabel;
use App\Enums\ScreeningStatus;
use App\Enums\SeatType;
use App\Mail\SendTicket;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use App\Models\Seat;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendTicketMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_ticket_email_without_unsupported_css(): void
    {
        $booking = $this->createBooking();

        $html = (new SendTicket($booking))->render();

        $this->assertStringContainsString('Bilet do kina', $html);
        $this->assertStringContainsString('Zamówienie nr BK12345', $html);
        $this->assertStringNotContainsString('border-radius', $html);
        $this->assertStringNotContainsString('font-weight: 700', $html);
        $this->assertStringNotContainsString('margin', $html);
        $this->assertStringNotContainsString('overflow', $html);
        $this->assertStringNotContainsString('padding:', $html);
    }

    private function createBooking(): Booking
    {
        $cinema = Cinema::factory()->create();

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

        $seat = Seat::query()->create([
            'hall_id' => $hall->getKey(),
            'row_label' => RowLabel::A,
            'seat_number' => 1,
            'seat_type' => SeatType::STANDARD,
            'pos_x' => 1,
            'pos_y' => 1,
            'is_active' => true,
        ]);

        $booking = Booking::query()->create([
            'screening_id' => $screening->getKey(),
            'customer_email' => 'jan@example.com',
            'booking_number' => 'BK12345',
            'status' => BookingStatus::CONFIRMED,
            'payment_method' => PaymentMethod::PAY_U,
        ]);

        $booking->bookedSeats()->create([
            'screening_id' => $screening->getKey(),
            'seat_id' => $seat->getKey(),
            'price' => 2100,
        ]);

        return $booking;
    }
}
