<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class SeatHoldStore
{
    /** @return Collection<int, string> */
    public function heldSeatIds(string $cinemaId, string $screeningId): Collection
    {
        $keys = Redis::client()->keys($this->createScreeningPattern($cinemaId, $screeningId));

        if (! is_array($keys)) {
            return collect();
        }

        return collect($keys)
            ->map(fn (string $key): string => Str::afterLast($key, ':'))
            ->values();
    }

    private function createScreeningPattern(string $cinemaId, string $screeningId): string
    {
        return sprintf('%s:%s:%s:*', SeatHoldService::KEY_PREFIX, $cinemaId, $screeningId);
    }
}
