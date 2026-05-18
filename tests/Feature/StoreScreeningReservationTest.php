<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Commands\SelectCinema;
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
use App\Models\User;
use App\Support\Booking\BookingNumberGenerator;
use App\Support\Seats\SeatHoldStore;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class StoreScreeningReservationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_pending_booking_and_redirects_to_the_payment_page(): void
    {
        config()->set('seat.prices.standard', 2100);
        config()->set('seat.prices.vip', 3300);
        Mail::fake();
        $guestToken = Uuid::uuid7()->toString();
        [$cinema, $screening, $firstSeat, $secondSeat] = $this->prepareScreening();

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->exactly(2))
            ->method('isHeldByOwner')
            ->willReturn(true);
        $this->instance(SeatHoldStore::class, $seatHoldStore);

        Redis::shouldReceive('client->get')
            ->twice()
            ->andReturn(
                json_encode(['user_identifier' => $guestToken], JSON_THROW_ON_ERROR),
                json_encode(['user_identifier' => $guestToken], JSON_THROW_ON_ERROR),
            );
        Redis::shouldReceive('client->del')
            ->twice()
            ->andReturn(1, 1);

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->withCookie('guest-token', $guestToken)
            ->post(route('screenings.book', $screening), [
                'email' => 'jan@example.com',
                'seatIds' => [$firstSeat->getKey(), $secondSeat->getKey()],
            ]);

        $booking = Booking::query()->firstOrFail();

        $response->assertRedirect(route('screenings.reservation-payment', [
            'screening' => $screening,
            'booking' => $booking,
        ]));

        $this->assertDatabaseHas('bookings', [
            'screening_id' => $screening->getKey(),
            'customer_email' => 'jan@example.com',
            'status' => BookingStatus::PENDING->value,
            'user_id' => null,
            'guest_id' => $guestToken,
            'payment_method' => PaymentMethod::PAY_U->value,
        ]);
        $this->assertDatabaseCount('booked_seats', 2);
        $this->assertDatabaseHas('booked_seats', [
            'booking_id' => $booking->getKey(),
            'seat_id' => $firstSeat->getKey(),
            'price' => 2100,
        ]);
        $this->assertDatabaseHas('booked_seats', [
            'booking_id' => $booking->getKey(),
            'seat_id' => $secondSeat->getKey(),
            'price' => 3300,
        ]);

        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_uses_the_authenticated_user_email_when_email_is_not_provided(): void
    {
        config()->set('seat.prices.standard', 2100);
        Mail::fake();
        $guestToken = Uuid::uuid7()->toString();
        $user = User::factory()->create([
            'email' => 'konto@example.com',
        ]);
        [$cinema, $screening, $firstSeat] = $this->prepareScreening();

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->once())
            ->method('isHeldByOwner')
            ->willReturn(true);
        $this->instance(SeatHoldStore::class, $seatHoldStore);

        Redis::shouldReceive('client->get')
            ->once()
            ->andReturn(json_encode(['user_identifier' => $user->getKey()], JSON_THROW_ON_ERROR));
        Redis::shouldReceive('client->del')
            ->once()
            ->andReturn(1);

        $response = $this
            ->actingAs($user)
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->withCookie('guest-token', $guestToken)
            ->post(route('screenings.book', $screening), [
                'seatIds' => [$firstSeat->getKey()],
            ]);

        $booking = Booking::query()->firstOrFail();

        $response->assertRedirect(route('screenings.reservation-payment', [
            'screening' => $screening,
            'booking' => $booking,
        ]));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->getKey(),
            'user_id' => $user->getKey(),
            'guest_id' => null,
            'customer_email' => 'konto@example.com',
            'status' => BookingStatus::PENDING->value,
        ]);

        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_retries_booking_creation_when_the_generated_booking_number_is_not_unique(): void
    {
        config()->set('seat.prices.standard', 2100);
        Mail::fake();
        $guestToken = Uuid::uuid7()->toString();
        [$cinema, $screening, $firstSeat] = $this->prepareScreening();

        Booking::query()->create([
            'screening_id' => $screening->getKey(),
            'customer_email' => 'existing@example.com',
            'booking_number' => 'BKDUPL1',
            'status' => BookingStatus::PENDING,
            'payment_method' => PaymentMethod::PAY_U,
        ]);

        $bookingNumberGenerator = $this->createMock(BookingNumberGenerator::class);
        $bookingNumberGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->willReturnOnConsecutiveCalls('BKDUPL1', 'BKUNIQ2');
        $this->instance(BookingNumberGenerator::class, $bookingNumberGenerator);

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->once())
            ->method('isHeldByOwner')
            ->willReturn(true);
        $this->instance(SeatHoldStore::class, $seatHoldStore);

        Redis::shouldReceive('client->get')
            ->once()
            ->andReturn(json_encode(['user_identifier' => $guestToken], JSON_THROW_ON_ERROR));
        Redis::shouldReceive('client->del')
            ->once()
            ->andReturn(1);

        $response = $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->withCookie('guest-token', $guestToken)
            ->post(route('screenings.book', $screening), [
                'email' => 'jan@example.com',
                'seatIds' => [$firstSeat->getKey()],
            ]);

        $booking = Booking::query()
            ->where('customer_email', 'jan@example.com')
            ->firstOrFail();

        $response->assertRedirect(route('screenings.reservation-payment', [
            'screening' => $screening,
            'booking' => $booking,
        ]));

        $this->assertSame('BKUNIQ2', $booking->booking_number);
        $this->assertDatabaseCount('bookings', 2);
    }

    #[Test]
    public function it_returns_a_validation_error_when_the_seat_hold_has_expired(): void
    {
        $guestToken = Uuid::uuid7()->toString();
        [$cinema, $screening, $firstSeat] = $this->prepareScreening();

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->once())
            ->method('isHeldByOwner')
            ->willReturn(false);
        $this->instance(SeatHoldStore::class, $seatHoldStore);

        $response = $this
            ->from(route('screenings.reservation-summary', [
                'screening' => $screening,
                'seatIds' => [$firstSeat->getKey()],
            ]))
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->withCookie('guest-token', $guestToken)
            ->post(route('screenings.book', $screening), [
                'email' => 'jan@example.com',
                'seatIds' => [$firstSeat->getKey()],
            ]);

        $response
            ->assertRedirect(route('screenings.reservation-summary', [
                'screening' => $screening,
                'seatIds' => [$firstSeat->getKey()],
            ]))
            ->assertInvalid([
                'seatIds' => 'Czas rezerwacji miejsc minął. Wybierz miejsca ponownie.',
            ]);

        $this->assertDatabaseCount('bookings', 0);
    }

    #[Test]
    public function it_confirms_the_booking_after_a_completed_payu_notification(): void
    {
        config()->set('seat.prices.standard', 2100);
        config()->set('services.payu.second_key', 'second-key');
        Mail::fake();
        $guestToken = Uuid::uuid7()->toString();
        [$cinema, $screening, $firstSeat] = $this->prepareScreening();

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->once())
            ->method('isHeldByOwner')
            ->willReturn(true);
        $this->instance(SeatHoldStore::class, $seatHoldStore);

        Redis::shouldReceive('client->get')
            ->once()
            ->andReturn(json_encode(['user_identifier' => $guestToken], JSON_THROW_ON_ERROR));
        Redis::shouldReceive('client->del')
            ->once()
            ->andReturn(1);

        $this
            ->withSession([
                SelectCinema::CINEMA_SESSION_KEY => $cinema->getKey(),
            ])
            ->withCookie('guest-token', $guestToken)
            ->post(route('screenings.book', $screening), [
                'email' => 'jan@example.com',
                'seatIds' => [$firstSeat->getKey()],
            ]);

        $booking = Booking::query()->firstOrFail();
        $content = (string) json_encode([
            'order' => [
                'extOrderId' => $booking->getKey(),
                'status' => 'COMPLETED',
            ],
        ]);

        $response = $this->call(
            'POST',
            route('payu.notify'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_OPENPAYU_SIGNATURE' => sprintf(
                    'sender=checkout;signature=%s;algorithm=MD5;content=DOCUMENT',
                    md5($content.'second-key'),
                ),
            ],
            $content,
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'OK',
            ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->getKey(),
            'status' => BookingStatus::CONFIRMED->value,
        ]);

        Mail::assertQueued(SendTicket::class, function (SendTicket $mail) use ($booking): bool {
            return $mail->hasTo('jan@example.com')
                && $mail->booking->is($booking);
        });
    }

    /**
     * @return array{0: Cinema, 1: Screening, 2: Seat, 3: Seat}
     */
    private function prepareScreening(): array
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

        return [$cinema, $screening, $firstSeat, $secondSeat];
    }
}
