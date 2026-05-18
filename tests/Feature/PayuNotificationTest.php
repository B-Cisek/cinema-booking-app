<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\ScreeningStatus;
use App\Mail\SendTicket;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayuNotificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_confirms_a_booking_for_a_completed_payu_notification(): void
    {
        Mail::fake();
        config()->set('services.payu.second_key', 'second-key');

        $booking = $this->prepareBooking();
        $content = $this->notificationContent($booking, 'COMPLETED');

        $response = $this->postPayuNotification($content);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'OK',
            ]);

        $this->assertSame(BookingStatus::CONFIRMED, $booking->refresh()->status);
        Mail::assertQueued(SendTicket::class);
    }

    #[Test]
    public function it_rejects_a_payu_notification_with_an_invalid_signature(): void
    {
        Mail::fake();
        config()->set('services.payu.second_key', 'second-key');

        $booking = $this->prepareBooking();
        $content = $this->notificationContent($booking, 'COMPLETED');

        $response = $this->call(
            'POST',
            route('payu.notify'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_OPENPAYU_SIGNATURE' => 'sender=checkout;signature=invalid;algorithm=MD5;content=DOCUMENT',
            ],
            $content,
        );

        $response
            ->assertBadRequest()
            ->assertJson([
                'message' => 'INVALID_SIGNATURE',
            ]);

        $this->assertSame(BookingStatus::PENDING, $booking->refresh()->status);
        Mail::assertNothingQueued();
    }

    #[Test]
    public function it_cancels_a_booking_for_a_cancelled_payu_notification(): void
    {
        Mail::fake();
        config()->set('services.payu.second_key', 'second-key');

        $booking = $this->prepareBooking();
        $content = $this->notificationContent($booking, 'CANCELED');

        $response = $this->postPayuNotification($content);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'OK',
            ]);

        $this->assertSame(BookingStatus::CANCELLED, $booking->refresh()->status);
        Mail::assertNothingQueued();
    }

    private function postPayuNotification(string $content): TestResponse
    {
        return $this->call(
            'POST',
            route('payu.notify'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_OPENPAYU_SIGNATURE' => $this->signatureHeader($content),
            ],
            $content,
        );
    }

    private function signatureHeader(string $content): string
    {
        return sprintf(
            'sender=checkout;signature=%s;algorithm=MD5;content=DOCUMENT',
            md5($content.'second-key'),
        );
    }

    private function notificationContent(Booking $booking, string $status): string
    {
        return (string) json_encode([
            'order' => [
                'orderId' => 'WZHF5FFDRJ140731GUEST000P01',
                'extOrderId' => $booking->getKey(),
                'status' => $status,
            ],
        ]);
    }

    private function prepareBooking(): Booking
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

        return Booking::query()->create([
            'screening_id' => $screening->getKey(),
            'booking_number' => 'ABC1234567',
            'status' => BookingStatus::PENDING,
            'customer_email' => 'jan@example.com',
            'payment_method' => PaymentMethod::PAY_U,
        ]);
    }
}
