<?php

namespace Tests\Unit;

use App\Data\SeatMap;
use App\Data\SeatView;
use App\Enums\RowLabel;
use App\Enums\SeatType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SeatMapTest extends TestCase
{
    public function test_it_returns_a_seat_by_human_label(): void
    {
        $seatMap = new SeatMap([
            new SeatView('seat-b2', RowLabel::B, 2, SeatType::VIP, 2, 2, true, false),
            new SeatView('seat-a3', RowLabel::A, 3, SeatType::STANDARD, 3, 1, true, true),
            new SeatView('seat-a1', RowLabel::A, 1, SeatType::STANDARD, 1, 1, true, false),
        ]);

        $seat = $seatMap->seat('a1');

        $this->assertSame('A1', $seat->label());
        $this->assertTrue($seat->isAvailable());
    }

    public function test_it_groups_rows_in_display_order(): void
    {
        $seatMap = new SeatMap([
            new SeatView('seat-b2', RowLabel::B, 2, SeatType::VIP, 2, 2, true, false),
            new SeatView('seat-a3', RowLabel::A, 3, SeatType::STANDARD, 3, 1, true, true),
            new SeatView('seat-a1', RowLabel::A, 1, SeatType::STANDARD, 1, 1, true, false),
        ]);

        $rows = $seatMap->rows();

        $this->assertCount(2, $rows);
        $this->assertSame('A', $rows[0]['label']);
        $this->assertSame('A1', $rows[0]['seats'][0]->label());
        $this->assertSame('A3', $rows[0]['seats'][1]->label());
        $this->assertSame('B', $rows[1]['label']);
    }

    public function test_it_throws_for_unknown_seat(): void
    {
        $seatMap = new SeatMap([
            new SeatView('seat-a1', RowLabel::A, 1, SeatType::STANDARD, 1, 1, true, false),
        ]);

        $this->expectException(InvalidArgumentException::class);

        $seatMap->seat('Z9');
    }
}
