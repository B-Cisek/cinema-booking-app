<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Resend\Laravel\Transport\ResendTransportFactory;
use Tests\TestCase;

class ResendMailTransportTest extends TestCase
{
    #[Test]
    public function it_resolves_the_resend_laravel_transport(): void
    {
        config([
            'mail.default' => 'resend',
            'services.resend.key' => 're_test_key',
        ]);

        $transport = Mail::mailer()->getSymfonyTransport();

        $this->assertInstanceOf(ResendTransportFactory::class, $transport);
    }
}
