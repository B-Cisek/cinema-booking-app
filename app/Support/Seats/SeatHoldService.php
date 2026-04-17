<?php

declare(strict_types=1);

namespace App\Support\Seats;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final readonly class SeatHoldService
{
    private const int HOLD_SECONDS = 300;

    public const string KEY_PREFIX = 'seat_hold';

    public function hold(string $cinemaId, string $screeningId, string $seatId, string $ownerIdentifier): ?string
    {
        $key = $this->createKey($cinemaId, $screeningId, $seatId);
        $expiresAt = CarbonImmutable::now()->addSeconds(self::HOLD_SECONDS)->toIso8601String();
        $payload = [
            'owner_identifier' => $ownerIdentifier,
            'expires_at' => $expiresAt,
        ];

        $wasHeld = Redis::client()
            ->set(
                key: $key,
                value: json_encode($payload, JSON_THROW_ON_ERROR),
                options: ['NX', 'EX' => self::HOLD_SECONDS]
            );

        if (! $wasHeld) {
            return null;
        }

        return $expiresAt;
    }

    public function release(string $cinemaId, string $screeningId, string $seatId, string $ownerIdentifier): void
    {
        $key = $this->createKey($cinemaId, $screeningId, $seatId);

        $current = Redis::client()->get($key);

        if ($current) {
            $payload = json_decode(json: $current, associative: true, flags: JSON_THROW_ON_ERROR);

            if ($payload['owner_identifier'] === $ownerIdentifier) {
                Redis::client()->del($key);
                Log::debug('SEAT_RELEASE', ['key' => $key]);
            }

        }
    }

    private function createKey(string $cinemaId, string $screeningId, string $seatId): string
    {
        return sprintf('%s:%s:%s:%s', self::KEY_PREFIX, $cinemaId, $screeningId, $seatId);
    }
}
