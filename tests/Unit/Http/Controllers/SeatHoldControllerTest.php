<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\Actions\SeatHold;
use App\Http\Controllers\SeatHoldController;
use App\Http\Requests\SeatHoldRequest;
use Illuminate\Container\Container;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeatHoldControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;
        $container->instance('translator', tap(new Translator(new ArrayLoader, 'en'), function (Translator $translator): void {
            $translator->addLines([
                'response.SEAT_HELD' => 'Seat has been held.',
            ], 'en');
        }));

        Container::setInstance($container);
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        parent::tearDown();
    }

    #[Test]
    public function it_returns_the_held_response_payload(): void
    {
        $seatHold = $this->createMock(SeatHold::class);
        $seatHold->expects($this->once())
            ->method('handle');

        $controller = new SeatHoldController($seatHold);
        $request = SeatHoldRequest::create('/seat-hold', 'POST');

        $response = $controller($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'message' => 'Seat has been held.',
            'code' => 'SEAT_HELD',
        ], $response->getData(true));
    }
}
