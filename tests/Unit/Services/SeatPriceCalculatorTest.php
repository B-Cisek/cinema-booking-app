<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Models\Seat;
use App\Services\SeatPriceCalculator;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class SeatPriceCalculatorTest extends TestCase
{
    #[Test]
    public function it_returns_the_price_defined_for_the_seat_type(): void
    {
        config()->set('seat.prices.standard', 2100);
        config()->set('seat.prices.vip', 3300);

        $calculator = $this->app->make(SeatPriceCalculator::class);

        $this->assertSame(2100, $calculator->forSeat($this->makeSeat(SeatType::STANDARD)));
        $this->assertSame(3300, $calculator->forSeat($this->makeSeat(SeatType::VIP)));
    }

    #[Test]
    public function it_throws_an_exception_when_the_price_is_missing_for_the_seat_type(): void
    {
        config()->set('seat.prices.standard', null);

        $calculator = $this->app->make(SeatPriceCalculator::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing seat price configuration for seat type [standard].');

        $calculator->forSeat($this->makeSeat(SeatType::STANDARD));
    }

    private function makeSeat(SeatType $seatType): Seat
    {
        return new Seat([
            'row_label' => RowLabel::A,
            'seat_number' => 1,
            'seat_type' => $seatType,
            'pos_x' => 1,
            'pos_y' => 1,
            'is_active' => true,
        ]);
    }
}
