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
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationPaymentPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_payu_for_pending_bookings(): void
    {
        config()->set('services.payu.base_url', 'https://secure.snd.payu.com');
        config()->set('services.payu.client_id', 'client-id');
        config()->set('services.payu.client_secret', 'client-secret');
        config()->set('services.payu.pos_id', 'pos-id');
        config()->set('services.payu.notify_url', 'https://example.com/payu/notif');

        Http::fake([
            'secure.snd.payu.com/pl/standard/user/oauth/authorize' => Http::response([
                'access_token' => 'payu-token',
            ]),
            'secure.snd.payu.com/api/v2_1/orders' => Http::response([
                'redirectUri' => 'https://secure.snd.payu.com/pay/booking-token',
            ]),
        ]);

        [$cinema, $screening, $booking] = $this->prepareBooking();

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->get(route('screenings.reservation-payment', [
                'screening' => $screening,
                'booking' => $booking,
            ]));

        $response->assertRedirect('https://secure.snd.payu.com/pay/booking-token');
    }

    #[Test]
    public function it_redirects_to_payu_for_payu_payments(): void
    {
        config()->set('services.payu.base_url', 'https://secure.snd.payu.com');
        config()->set('services.payu.client_id', 'client-id');
        config()->set('services.payu.client_secret', 'client-secret');
        config()->set('services.payu.pos_id', 'pos-id');
        config()->set('services.payu.notify_url', 'https://example.com/payu/notif');

        Http::fake([
            'secure.snd.payu.com/pl/standard/user/oauth/authorize' => Http::response([
                'access_token' => 'payu-token',
            ]),
            'secure.snd.payu.com/api/v2_1/orders' => Http::response([
                'redirectUri' => 'https://secure.snd.payu.com/pay/booking-token',
            ]),
        ]);

        [$cinema, $screening, $booking] = $this->prepareBooking();

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->get(route('screenings.reservation-payment', [
                'screening' => $screening,
                'booking' => $booking,
            ]));

        $response->assertRedirect('https://secure.snd.payu.com/pay/booking-token');

        Http::assertSentCount(2);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://secure.snd.payu.com/api/v2_1/orders'
            && $request['totalAmount'] === '5600'
            && $request['extOrderId'] === $booking->getKey()
            && $request['continueUrl'] === route('screenings.reservation-success', [
                'screening' => $screening,
                'booking' => $booking,
            ])
            && $request['notifyUrl'] === 'https://example.com/payu/notif'
            && $request['products'][0]['unitPrice'] === '5600'
            && $request['products'][0]['quantity'] === '2');
    }

    #[Test]
    public function it_redirects_to_the_payu_location_header_when_the_order_response_is_a_redirect(): void
    {
        config()->set('services.payu.base_url', 'https://secure.snd.payu.com');
        config()->set('services.payu.client_id', 'client-id');
        config()->set('services.payu.client_secret', 'client-secret');
        config()->set('services.payu.pos_id', 'pos-id');

        Http::fake([
            'secure.snd.payu.com/pl/standard/user/oauth/authorize' => Http::response([
                'access_token' => 'payu-token',
            ]),
            'secure.snd.payu.com/api/v2_1/orders' => Http::response('', 302, [
                'Location' => 'https://secure.snd.payu.com/pay/booking-token',
            ]),
        ]);

        [$cinema, $screening, $booking] = $this->prepareBooking();

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->get(route('screenings.reservation-payment', [
                'screening' => $screening,
                'booking' => $booking,
            ]));

        $response->assertRedirect('https://secure.snd.payu.com/pay/booking-token');
    }

    /**
     * @return array{0: Cinema, 1: Screening, 2: Booking}
     */
    private function prepareBooking(array $overrides = []): array
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
            'payment_method' => $overrides['payment_method'] ?? PaymentMethod::PAY_U,
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
            'screening_id' => $screening->getKey(),
            'seat_id' => $firstSeat->getKey(),
            'price' => 2200,
        ]);

        BookedSeat::query()->create([
            'booking_id' => $booking->getKey(),
            'screening_id' => $screening->getKey(),
            'seat_id' => $secondSeat->getKey(),
            'price' => 3400,
        ]);

        return [$cinema, $screening, $booking];
    }
}
