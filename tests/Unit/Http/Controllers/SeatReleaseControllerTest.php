<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\Actions\SeatRelease;
use App\Http\Controllers\SeatReleaseController;
use App\Http\Requests\SeatReleaseRequest;
use Illuminate\Container\Container;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeatReleaseControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;
        $container->instance('translator', tap(new Translator(new ArrayLoader, 'en'), function (Translator $translator): void {
            $translator->addLines([
                'response.SEAT_RELEASED' => 'Seat has been released.',
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
    public function it_returns_the_released_response_code(): void
    {
        $seatRelease = $this->createMock(SeatRelease::class);
        $seatRelease->expects($this->once())
            ->method('handle');

        $controller = new SeatReleaseController($seatRelease);
        $request = SeatReleaseRequest::create('/seat-release', 'POST');

        $response = $controller($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'message' => 'Seat has been released.',
            'code' => 'SEAT_RELEASED',
        ], $response->getData(true));
    }
}
