<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Support\Seats\SeatHoldStore;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class SeatHoldStoreTest extends TestCase
{
    #[Test]
    public function it_returns_an_empty_collection_when_the_redis_store_is_unavailable(): void
    {
        $container = new Container;
        $logger = new class
        {
            /** @var array{message: string, context: array<string, string>}|null */
            public ?array $warning = null;

            /**
             * @param  array<string, string>  $context
             */
            public function warning(string $message, array $context): void
            {
                $this->warning = [
                    'message' => $message,
                    'context' => $context,
                ];
            }
        };

        $container->instance('redis', new class
        {
            public function client(): never
            {
                throw new RuntimeException('Connection refused');
            }
        });
        $container->instance('log', $logger);

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($container);

        $heldSeatIds = (new SeatHoldStore)->heldSeatIds('cinema-1', 'screening-1');

        $this->assertTrue($heldSeatIds->isEmpty());
        $this->assertSame([
            'message' => 'SEAT_HOLD_STORE_UNAVAILABLE',
            'context' => [
                'cinema_id' => 'cinema-1',
                'screening_id' => 'screening-1',
                'message' => 'Connection refused',
            ],
        ], $logger->warning);

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
    }
}
