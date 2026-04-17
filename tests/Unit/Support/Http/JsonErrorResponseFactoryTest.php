<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Http;

use App\Enums\ResponseCode;
use App\Support\Http\JsonResponseFactory;
use Illuminate\Container\Container;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JsonErrorResponseFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;
        $container->instance('translator', tap(new Translator(new ArrayLoader, 'en'), function (Translator $translator): void {
            $translator->addLines([
                'response.SEAT_ALREADY_RESERVED' => 'Seat already reserved.',
            ], 'en');
        }));

        Container::setInstance($container);
    }

    protected function tearDown(): void
    {
        Container::setInstance();

        parent::tearDown();
    }

    #[Test]
    public function it_creates_a_conflict_response_for_a_reserved_seat(): void
    {
        $response = (new JsonResponseFactory)->make(ResponseCode::SEAT_ALREADY_RESERVED);

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame(
            [
                'message' => 'Seat already reserved.',
                'code' => 'SEAT_ALREADY_RESERVED',
            ],
            $response->getData(true),
        );
    }

    #[Test]
    public function it_merges_additional_context_into_the_json_payload(): void
    {
        $response = (new JsonResponseFactory)->make(
            ResponseCode::SEAT_ALREADY_RESERVED,
            ['seat_id' => 'seat-1'],
        );

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame(
            [
                'message' => 'Seat already reserved.',
                'code' => 'SEAT_ALREADY_RESERVED',
                'seat_id' => 'seat-1',
            ],
            $response->getData(true),
        );
    }
}
