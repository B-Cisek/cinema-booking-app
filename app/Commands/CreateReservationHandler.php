<?php

declare(strict_types=1);

namespace App\Commands;

use App\Enums\PaymentMethod;
use App\Models\Booking;
use App\Models\Screening;
use App\Models\Seat;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Support\Booking\BookingNumberGenerator;
use App\Support\Pricing\SeatPriceCalculator;
use App\Support\Seats\SeatHoldService;
use App\Support\Seats\SeatHoldStore;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

readonly class CreateReservationHandler
{
    public function __construct(
        private BookingRepository $bookingRepository,
        private BookingNumberGenerator $bookingNumberGenerator,
        private SeatHoldService $seatHoldService,
        private SeatHoldStore $seatHoldStore,
        private SeatPriceCalculator $seatPriceCalculator,
        private UserRepository $userRepository,
    ) {}

    /**
     * @param  array<int, string>  $seatIds
     */
    public function handle(
        Screening $screening,
        array $seatIds,
        string $userIdentifier,
        PaymentMethod $paymentMethod,
        ?string $customerEmail
    ): Booking {
        $screening->loadMissing('hall');

        $selectedSeats = $screening->hall->seats()
            ->whereIn('id', $seatIds)
            ->where('is_active', true)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        $this->ensureSeatsCanBeBooked(
            selectedSeats: $selectedSeats,
            screening: $screening,
            cinemaId: $screening->hall->cinema_id,
            userIdentifier: $userIdentifier,
            seatIds: $seatIds,
        );

        return $this->createBooking(
            ownerIdentifier: $userIdentifier,
            customerEmail: $customerEmail,
            selectedSeats: $selectedSeats,
            cinemaId: $screening->hall->cinema_id,
            screeningId: $screening->getKey(),
            paymentMethod: $paymentMethod
        );
    }

    private function createBooking(
        string $ownerIdentifier,
        ?string $customerEmail,
        Collection $selectedSeats,
        string $cinemaId,
        string $screeningId,
        PaymentMethod $paymentMethod,
    ): Booking {
        $owner = $this->userRepository->get($ownerIdentifier);
        $resolvedCustomerEmail = $customerEmail ?? $owner?->email;

        if ($resolvedCustomerEmail === null) {
            throw ValidationException::withMessages([
                'email' => 'Adres e-mail jest wymagany do utworzenia rezerwacji.',
            ]);
        }

        DB::beginTransaction();

        try {
            $booking = $this->bookingRepository->create(
                bookingNumber: $this->generateUniqueBookingNumber(),
                screeningId: $screeningId,
                customerEmail: $resolvedCustomerEmail,
                paymentMethod: $paymentMethod,
                userId: $owner?->getKey(),
                guestId: $owner === null ? $ownerIdentifier : null,
            );

            $booking->bookedSeats()->createMany(
                $selectedSeats->map(
                    fn (Seat $seat): array => [
                        'seat_id' => $seat->getKey(),
                        'screening_id' => $screeningId,
                        'price' => $this->seatPriceCalculator->forSeat($seat),
                    ],
                )->all(),
            );

            foreach ($selectedSeats as $seat) {
                $this->seatHoldService->release(
                    cinemaId: $cinemaId,
                    screeningId: $screeningId,
                    seatId: $seat->getKey(),
                    userIdentifier: $ownerIdentifier,
                );
            }

            DB::commit();

            return $booking;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function generateUniqueBookingNumber(): string
    {
        $attempts = 0;

        do {
            $bookingNumber = $this->bookingNumberGenerator->generate();
            $attempts++;

            if (! $this->bookingRepository->bookingNumberExists($bookingNumber)) {
                return $bookingNumber;
            }
        } while ($attempts < 5);

        throw new RuntimeException('Could not generate unique booking number.');
    }

    /**
     * @param  Collection<int, Seat>  $selectedSeats
     * @param  array<int, string>  $seatIds
     */
    private function ensureSeatsCanBeBooked(
        Collection $selectedSeats,
        Screening $screening,
        string $cinemaId,
        string $userIdentifier,
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
                userIdentifier: $userIdentifier,
            )) {
                throw ValidationException::withMessages([
                    'seatIds' => 'Czas rezerwacji miejsc minął. Wybierz miejsca ponownie.',
                ]);
            }
        }
    }
}
