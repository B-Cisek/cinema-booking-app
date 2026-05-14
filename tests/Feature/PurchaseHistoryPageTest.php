<?php

declare(strict_types=1);

namespace Tests\Feature;

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
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurchaseHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_guests_from_purchase_history(): void
    {
        $this->get(route('purchase-history'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function it_shows_paginated_purchase_history_for_the_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email' => 'historia@example.com',
        ]);

        $otherUser = User::factory()->create();

        [$firstBooking] = $this->createConfirmedBookingForUser(
            $user,
            [
                'booking_number' => 'BK00000001',
                'created_at' => CarbonImmutable::parse('2026-05-01 10:00:00'),
                'title' => 'Diuna',
                'city' => 'Warszawa',
            ],
        );

        $this->createConfirmedBookingForUser(
            $user,
            [
                'booking_number' => 'BK00000002',
                'created_at' => CarbonImmutable::parse('2026-05-02 10:00:00'),
                'title' => 'Matrix',
                'payment_method' => PaymentMethod::PRZELEWY24,
            ],
        );

        $this->createConfirmedBookingForUser(
            $otherUser,
            [
                'booking_number' => 'BK00000003',
                'created_at' => CarbonImmutable::parse('2026-05-03 10:00:00'),
                'title' => 'Obcy',
            ],
        );

        $this->createPendingBookingForUser($user);

        $response = $this->actingAs($user)->get(route('purchase-history'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('PurchaseHistory')
                ->where('bookings.pagination.total', 2)
                ->where('bookings.pagination.current_page', 1)
                ->has('bookings.data', 2)
                ->where('bookings.data.0.number', 'BK00000002')
                ->where('bookings.data.0.screening.movie.title', 'Matrix')
                ->where('bookings.data.1.number', 'BK00000001')
                ->where('bookings.data.1.screening.movie.title', 'Diuna')
                ->where('bookings.data.1.payment_method', 'PayU')
                ->where('bookings.data.1.status', 'Opłacona')
                ->where('bookings.data.1.total', 5600)
                ->where('bookings.data.1.seats.0.label', 'A1')
                ->where('bookings.data.1.seats.1.label', 'A2')
                ->where('bookings.data.1.screening.hall.cinema.city', 'Warszawa')
                ->where('bookings.data.1.id', $firstBooking->getKey())
            );
    }

    #[Test]
    public function it_supports_pagination_for_purchase_history(): void
    {
        $user = User::factory()->create();

        for ($index = 1; $index <= 6; $index++) {
            $this->createConfirmedBookingForUser(
                $user,
                [
                    'booking_number' => sprintf('BK%08d', $index),
                    'created_at' => CarbonImmutable::parse(sprintf('2026-05-%02d 10:00:00', $index)),
                    'title' => sprintf('Film %d', $index),
                ],
            );
        }

        $response = $this->actingAs($user)->get(route('purchase-history', [
            'page' => 2,
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('PurchaseHistory')
                ->where('bookings.pagination.current_page', 2)
                ->where('bookings.pagination.last_page', 2)
                ->where('bookings.pagination.total', 6)
                ->has('bookings.data', 1)
                ->where('bookings.data.0.number', 'BK00000001')
            );
    }

    /**
     * @param array{
     *     booking_number?: string,
     *     created_at?: CarbonImmutable,
     *     title?: string,
     *     city?: string,
     *     payment_method?: PaymentMethod
     * } $overrides
     * @return array{0: Booking, 1: Screening}
     */
    private function createConfirmedBookingForUser(User $user, array $overrides = []): array
    {
        $cinema = Cinema::factory()->create([
            'city' => $overrides['city'] ?? 'Krakow',
            'street' => 'ul. Szewska 10',
        ]);

        $hall = Hall::query()->create([
            'cinema_id' => $cinema->getKey(),
            'name' => 'main',
            'label' => 'Sala 1',
        ]);

        $movie = Movie::query()->create([
            'title' => $overrides['title'] ?? 'Interstellar',
            'description' => 'Sci-fi epic',
            'duration' => 155,
            'poster_url' => 'https://example.com/poster.jpg',
            'is_active' => true,
        ]);

        $startsAt = $overrides['created_at'] ?? CarbonImmutable::parse('2026-05-01 10:00:00');

        $screening = Screening::query()->create([
            'movie_id' => $movie->getKey(),
            'hall_id' => $hall->getKey(),
            'status' => ScreeningStatus::SCHEDULED,
            'starts_at' => $startsAt->addDays(1)->setTime(18, 0),
            'ends_at' => $startsAt->addDays(1)->setTime(20, 35),
        ]);

        $booking = Booking::query()->create([
            'user_id' => $user->getKey(),
            'screening_id' => $screening->getKey(),
            'booking_number' => $overrides['booking_number'] ?? 'BKDEFAULT',
            'status' => BookingStatus::CONFIRMED,
            'customer_email' => $user->email,
            'payment_method' => $overrides['payment_method'] ?? PaymentMethod::PAY_U,
        ]);

        $booking->forceFill([
            'created_at' => $startsAt,
            'updated_at' => $startsAt,
        ])->save();

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

        return [$booking, $screening];
    }

    private function createPendingBookingForUser(User $user): Booking
    {
        [$confirmedBooking, $screening] = $this->createConfirmedBookingForUser($user, [
            'booking_number' => 'BKPENDING1',
            'created_at' => CarbonImmutable::parse('2026-04-25 10:00:00'),
        ]);

        $confirmedBooking->delete();

        return Booking::query()->create([
            'user_id' => $user->getKey(),
            'screening_id' => $screening->getKey(),
            'booking_number' => 'BKPENDING1',
            'status' => BookingStatus::PENDING,
            'customer_email' => $user->email,
            'payment_method' => PaymentMethod::PAY_U,
        ]);
    }
}
