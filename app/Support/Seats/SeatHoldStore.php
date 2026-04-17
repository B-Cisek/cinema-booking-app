<?php

declare(strict_types=1);

namespace App\Support\Seats;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Throwable;

class SeatHoldStore
{
    /** @return Collection<int, string> */
    public function heldSeatIds(string $cinemaId, string $screeningId): Collection
    {
        try {
            $keys = Redis::client()->keys($this->createScreeningPattern($cinemaId, $screeningId));
        } catch (Throwable $exception) {
            Log::warning('SEAT_HOLD_STORE_UNAVAILABLE', [
                'cinema_id' => $cinemaId,
                'screening_id' => $screeningId,
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }

        if (! is_array($keys)) {
            return collect();
        }

        return collect($keys)
            ->map(fn (string $key): string => Str::afterLast($key, ':'))
            ->values();
    }

    public function isHeldByOwner(
        string $cinemaId,
        string $screeningId,
        string $seatId,
        string $ownerIdentifier,
    ): bool {
        try {
            $payload = Redis::client()->get(
                sprintf(
                    '%s:%s:%s:%s',
                    SeatHoldService::KEY_PREFIX,
                    $cinemaId,
                    $screeningId,
                    $seatId,
                ),
            );
        } catch (Throwable $exception) {
            Log::warning('SEAT_HOLD_STORE_UNAVAILABLE', [
                'cinema_id' => $cinemaId,
                'screening_id' => $screeningId,
                'seat_id' => $seatId,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        if (! is_string($payload) || $payload === '') {
            return false;
        }

        $decodedPayload = json_decode($payload, true);

        if (! is_array($decodedPayload)) {
            return false;
        }

        return ($decodedPayload['owner_identifier'] ?? null) === $ownerIdentifier;
    }

    private function createScreeningPattern(string $cinemaId, string $screeningId): string
    {
        return sprintf('%s:%s:%s:*', SeatHoldService::KEY_PREFIX, $cinemaId, $screeningId);
    }
}
