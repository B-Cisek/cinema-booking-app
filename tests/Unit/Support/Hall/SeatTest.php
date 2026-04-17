<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Hall;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Support\Hall\Seat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeatTest extends TestCase
{
    #[Test]
    public function it_builds_the_seat_label_from_row_and_number(): void
    {
        $seat = new Seat(
            id: 'seat-1',
            row: RowLabel::C,
            seatNumber: 7,
            seatType: SeatType::VIP,
            posX: 7,
            posY: 3,
            isActive: true,
            isBooked: false,
        );

        $this->assertSame('C7', $seat->label());
    }

    #[Test]
    #[DataProvider('availabilityCases')]
    public function it_determines_if_a_seat_is_available(
        bool $isActive,
        bool $isBooked,
        bool $expectedAvailability,
    ): void {
        $seat = new Seat(
            id: 'seat-1',
            row: RowLabel::A,
            seatNumber: 1,
            seatType: SeatType::STANDARD,
            posX: 1,
            posY: 1,
            isActive: $isActive,
            isBooked: $isBooked,
        );

        $this->assertSame($expectedAvailability, $seat->isAvailable());
    }

    /**
     * @return array<string, array{isActive: bool, isBooked: bool, expectedAvailability: bool}>
     */
    public static function availabilityCases(): array
    {
        return [
            'active and not booked' => [
                'isActive' => true,
                'isBooked' => false,
                'expectedAvailability' => true,
            ],
            'active but booked' => [
                'isActive' => true,
                'isBooked' => true,
                'expectedAvailability' => false,
            ],
            'inactive and not booked' => [
                'isActive' => false,
                'isBooked' => false,
                'expectedAvailability' => false,
            ],
        ];
    }
}
