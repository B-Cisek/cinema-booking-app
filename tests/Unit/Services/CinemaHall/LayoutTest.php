<?php

declare(strict_types=1);

namespace Tests\Unit\Services\CinemaHall;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Services\CinemaHall\Layout;
use App\Services\CinemaHall\Seat;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    #[Test]
    public function it_sorts_seats_and_finds_them_using_a_normalized_label(): void
    {
        config()->set('app.hall.max_seats_per_row', 5);

        $layout = new Layout(collect([
            $this->makeSeat(id: 'seat-b', row: RowLabel::A, seatNumber: 2, posX: 2, posY: 1),
            $this->makeSeat(id: 'seat-c', row: RowLabel::B, seatNumber: 1, posX: 1, posY: 2),
            $this->makeSeat(id: 'seat-a', row: RowLabel::A, seatNumber: 1, posX: 1, posY: 1),
        ]));

        $this->assertTrue($layout->hasSeat(' a1 '));
        $this->assertSame('seat-a', $layout->seat(' a1 ')->id);
        $this->assertSame(
            ['seat-a', 'seat-b', 'seat-c'],
            array_map(fn (Seat $seat): string => $seat->id, $layout->all()),
        );
    }

    #[Test]
    public function it_returns_only_available_seats(): void
    {
        $layout = new Layout(collect([
            $this->makeSeat(id: 'available', row: RowLabel::A, seatNumber: 1, posX: 1, posY: 1),
            $this->makeSeat(id: 'booked', row: RowLabel::A, seatNumber: 2, posX: 2, posY: 1, isBooked: true),
            $this->makeSeat(id: 'inactive', row: RowLabel::A, seatNumber: 3, posX: 3, posY: 1, isActive: false),
        ]));

        $this->assertSame(
            ['available'],
            array_map(fn (Seat $seat): string => $seat->id, $layout->availableSeats()),
        );
    }

    #[Test]
    public function it_builds_rows_with_fixed_width_and_empty_positions(): void
    {
        config()->set('app.hall.max_seats_per_row', 4);

        $layout = new Layout(collect([
            $this->makeSeat(id: 'a1', row: RowLabel::A, seatNumber: 1, posX: 1, posY: 1),
            $this->makeSeat(id: 'a3', row: RowLabel::A, seatNumber: 3, posX: 3, posY: 1),
            $this->makeSeat(id: 'b2', row: RowLabel::B, seatNumber: 2, posX: 2, posY: 2),
        ]));

        $rows = $layout->rows();

        $this->assertSame('A', $rows[0]['label']);
        $this->assertSame(['a1', null, 'a3', null], $this->seatIds($rows[0]['seats']));
        $this->assertSame('B', $rows[1]['label']);
        $this->assertSame([null, 'b2', null, null], $this->seatIds($rows[1]['seats']));
    }

    #[Test]
    public function it_returns_only_row_labels_that_exist_in_the_layout(): void
    {
        config()->set('app.hall.max_seats_per_row', 4);

        $layout = new Layout(collect([
            $this->makeSeat(id: 'b1', row: RowLabel::B, seatNumber: 1, posX: 1, posY: 2),
            $this->makeSeat(id: 'd2', row: RowLabel::D, seatNumber: 2, posX: 2, posY: 4),
        ]));

        $this->assertSame(['B', 'D'], array_column($layout->rows(), 'label'));
    }

    #[Test]
    public function it_throws_an_exception_when_the_seat_does_not_exist(): void
    {
        $layout = new Layout(collect([
            $this->makeSeat(id: 'a1', row: RowLabel::A, seatNumber: 1, posX: 1, posY: 1),
        ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Seat [B2] does not exist in this seat map.');

        $layout->seat('B2');
    }

    private function makeSeat(
        string $id,
        RowLabel $row,
        int $seatNumber,
        int $posX,
        int $posY,
        bool $isActive = true,
        bool $isBooked = false,
    ): Seat {
        return new Seat(
            id: $id,
            row: $row,
            seatNumber: $seatNumber,
            seatType: SeatType::STANDARD,
            posX: $posX,
            posY: $posY,
            isActive: $isActive,
            isBooked: $isBooked,
        );
    }

    /**
     * @param  array<int, Seat|null>  $seats
     * @return array<int, string|null>
     */
    private function seatIds(array $seats): array
    {
        return array_map(
            fn (?Seat $seat): ?string => $seat?->id,
            $seats,
        );
    }
}
