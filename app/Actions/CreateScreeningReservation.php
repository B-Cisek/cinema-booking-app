<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\BookingStatus;
use App\Mail\SendTicket;
use App\Models\Booking;
use App\Models\Screening;
use App\Models\Seat;
use App\Repositories\BookingRepository;
use App\Services\CinemaResolver;
use App\Services\GuestTokenHandler;
use App\Services\SeatHoldService;
use App\Services\SeatHoldStore;
use App\Services\SeatPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly class CreateScreeningReservation
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private CinemaResolver $cinemaResolver,
        private GuestTokenHandler $guestTokenHandler,
        private SeatHoldService $seatHoldService,
        private SeatHoldStore $seatHoldStore,
        private SeatPriceCalculator $seatPriceCalculator,
    ) {}

    /**
     * @param  array<int, string>  $seatIds
     */
    public function handle(
        Request $request,
        Screening $screening,
        string $customerEmail,
        array $seatIds,
    ): Booking {
        $cinema = $this->cinemaResolver->resolve($request);

        if ($cinema === null) {
            throw ValidationException::withMessages([
                'seatIds' => 'Najpierw wybierz kino.',
            ]);
        }

        $screening->loadMissing('hall');

        $ownerIdentifier = $this->guestTokenHandler->resolve($request);
        $selectedSeats = $screening->hall->seats()
            ->whereIn('id', $seatIds)
            ->where('is_active', true)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        $this->ensureSeatsCanBeBooked(
            selectedSeats: $selectedSeats,
            screening: $screening,
            cinemaId: $cinema->getKey(),
            ownerIdentifier: $ownerIdentifier,
            seatIds: $seatIds,
        );

        /** @var Booking $booking */
        $booking = DB::transaction(function () use (
            $customerEmail,
            $ownerIdentifier,
            $screening,
            $selectedSeats,
            $request,
        ): Booking {
            $booking = $screening->bookings()->create([
                'user_id' => $request->user()?->getAuthIdentifier(),
                'booking_number' => $this->generateBookingNumber(),
                'status' => BookingStatus::CONFIRMED,
                'customer_email' => $customerEmail,
            ]);

            $booking->bookedSeats()->createMany(
                $selectedSeats->map(
                    fn (Seat $seat): array => [
                        'seat_id' => $seat->getKey(),
                        'price' => $this->seatPriceCalculator->forSeat($seat),
                    ],
                )->all(),
            );

            foreach ($selectedSeats as $seat) {
                $this->seatHoldService->release(
                    cinemaId: $screening->hall->cinema_id,
                    screeningId: $screening->getKey(),
                    seatId: $seat->getKey(),
                    ownerIdentifier: $ownerIdentifier,
                );
            }

            return $booking;
        });

        $booking->loadMissing([
            'screening.movie',
            'screening.hall.cinema',
            'bookedSeats.seat',
        ]);

        Mail::to($booking->customer_email)->queue(new SendTicket($booking));

        return $booking;
    }

    /**
     * @param  Collection<int, Seat>  $selectedSeats
     * @param  array<int, string>  $seatIds
     */
    private function ensureSeatsCanBeBooked(
        Collection $selectedSeats,
        Screening $screening,
        string $cinemaId,
        string $ownerIdentifier,
        array $seatIds,
    ): void {
        if ($selectedSeats->count() !== count($seatIds)) {
            throw ValidationException::withMessages([
                'seatIds' => 'Wybrane miejsca nie są już dostępne.',
            ]);
        }

        foreach ($selectedSeats as $seat) {
            if ($this->bookingRepository->isSeatBooked($screening->getKey(), $seat->getKey())) {
                throw ValidationException::withMessages([
                    'seatIds' => 'Wybrane miejsca nie są już dostępne.',
                ]);
            }

            if (! $this->seatHoldStore->isHeldByOwner(
                cinemaId: $cinemaId,
                screeningId: $screening->getKey(),
                seatId: $seat->getKey(),
                ownerIdentifier: $ownerIdentifier,
            )) {
                throw ValidationException::withMessages([
                    'seatIds' => 'Czas rezerwacji miejsc minął. Wybierz miejsca ponownie.',
                ]);
            }
        }
    }

    private function generateBookingNumber(): string
    {
        return Str::upper(Str::random(10));
    }
}
