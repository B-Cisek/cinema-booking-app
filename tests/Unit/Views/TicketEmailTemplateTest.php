<?php

declare(strict_types=1);

namespace Tests\Unit\Views;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TicketEmailTemplateTest extends TestCase
{
    #[Test]
    public function it_contains_the_expected_booking_sections(): void
    {
        $viewPath = dirname(__DIR__, 3).'/resources/views/mail/ticket.blade.php';
        $contents = file_get_contents($viewPath);

        $this->assertIsString($contents);
        $this->assertStringContainsString('Zamówienie nr', $contents);
        $this->assertStringContainsString('Twoje miejsca', $contents);
        $this->assertStringContainsString('Pokaż ten e-mail przy wejściu na salę.', $contents);
        $this->assertStringContainsString('$booking->booking_number', $contents);
        $this->assertStringContainsString('$screening->starts_at', $contents);
        $this->assertStringContainsString('$movie->title', $contents);
    }
}
