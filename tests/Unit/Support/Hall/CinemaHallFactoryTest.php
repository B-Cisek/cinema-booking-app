<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Hall;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Models\Booking;
use App\Models\Hall;
use App\Models\Screening;
use App\Models\Seat;
use App\Repositories\ScreeningRepository;
use App\Support\Hall\CinemaHallFactory;
use App\Support\Seats\SeatHoldStore;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CinemaHallFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_a_layout_for_a_screening_with_booked_held_and_available_seats(): void
    {
        $screening = new Screening([
            'id' => 'screening-1',
        ]);

        $screening->setRelation('hall', new Hall([
            'id' => 'hall-1',
            'cinema_id' => 'cinema-1',
        ]));

        $screening->hall->setRelation('seats', collect([
            $this->makeSeatModel(
                id: 'seat-2',
                row: RowLabel::A,
                seatNumber: 2,
                seatType: SeatType::VIP,
                posX: 2,
                posY: 1,
                isActive: true,
            ),
            $this->makeSeatModel(
                id: 'seat-1',
                row: RowLabel::A,
                seatNumber: 1,
                seatType: SeatType::STANDARD,
                posX: 1,
                posY: 1,
                isActive: true,
            ),
            $this->makeSeatModel(
                id: 'seat-3',
                row: RowLabel::B,
                seatNumber: 1,
                seatType: SeatType::WHEELCHAIR,
                posX: 1,
                posY: 2,
                isActive: false,
            ),
        ]));

        $firstBooking = $this->makeBooking(['seat-2']);
        $secondBooking = $this->makeBooking(['seat-99']);

        $screening->setRelation('bookings', collect([$firstBooking, $secondBooking]));

        $screeningRepository = $this->createMock(ScreeningRepository::class);
        $screeningRepository
            ->expects($this->once())
            ->method('getById')
            ->with('screening-1')
            ->willReturn($screening);

        $seatHoldStore = $this->createMock(SeatHoldStore::class);
        $seatHoldStore
            ->expects($this->once())
            ->method('heldSeatIds')
            ->with('cinema-1', 'screening-1')
            ->willReturn(collect(['seat-1']));

        $layout = new CinemaHallFactory($screeningRepository, $seatHoldStore)->forScreening('screening-1');

        $this->assertSame(['A1', 'A2', 'B1'], array_map(
            fn (\App\Support\Hall\Seat $seat): string => $seat->label(),
            $layout->all(),
        ));

        $this->assertFalse($layout->seat('A1')->isAvailable());
        $this->assertTrue($layout->seat('A1')->isBooked);
        $this->assertFalse($layout->seat('A2')->isAvailable());
        $this->assertTrue($layout->seat('A2')->isBooked);
        $this->assertFalse($layout->seat('B1')->isAvailable());
        $this->assertSame(SeatType::WHEELCHAIR, $layout->seat('B1')->seatType);
    }

    /**
     * @param  array<int, string>  $bookedSeatIds
     */
    private function makeBooking(array $bookedSeatIds): Booking
    {
        $relation = new class($bookedSeatIds) extends HasMany
        {
            /**
             * @param  array<int, string>  $bookedSeatIds
             */
            public function __construct(private array $bookedSeatIds) {}

            public function pluck($column, $key = null)
            {
                TestCase::assertSame('seat_id', $column);

                return collect($this->bookedSeatIds);
            }
        };

        $booking = $this->getMockBuilder(Booking::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['bookedSeats'])
            ->getMock();

        $booking
            ->method('bookedSeats')
            ->willReturn($relation);

        return $booking;
    }

    private function makeSeatModel(
        string $id,
        RowLabel $row,
        int $seatNumber,
        SeatType $seatType,
        int $posX,
        int $posY,
        bool $isActive,
    ): Seat {
        $seat = new Seat([
            'row_label' => $row,
            'seat_number' => $seatNumber,
            'seat_type' => $seatType,
            'pos_x' => $posX,
            'pos_y' => $posY,
            'is_active' => $isActive,
        ]);

        $seat->id = $id;

        return $seat;
    }
}
