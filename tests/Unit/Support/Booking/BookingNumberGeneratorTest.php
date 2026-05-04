<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Booking;

use App\Support\Booking\BookingNumberGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BookingNumberGeneratorTest extends TestCase
{
    #[Test]
    public function it_generates_a_booking_number_with_the_expected_prefix_length_and_character_set(): void
    {
        $bookingNumber = (new BookingNumberGenerator)->generate();

        $this->assertSame(8, strlen($bookingNumber));
        $this->assertStringStartsWith('BK', $bookingNumber);
        $this->assertMatchesRegularExpression('/^BK[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{6}$/', $bookingNumber);
    }
}
