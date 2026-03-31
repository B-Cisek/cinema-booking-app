<?php

namespace App\Repositories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class CinemaRepository
{
    private const string CINEMAS_KEY = 'cinemas';

    public function getForSelect(): Collection
    {
        $cachedCinemas = Cache::get(self::CINEMAS_KEY);

        if (is_array($cachedCinemas)) {
            return Cinema::hydrate($cachedCinemas);
        }

        if ($cachedCinemas !== null) {
            Cache::forget(self::CINEMAS_KEY);
        }

        $cinemas = Cache::rememberForever(
            key: self::CINEMAS_KEY,
            callback: fn () => Cinema::query()
                ->select('cinemas.id', 'cinemas.street', 'cinemas.city')
                ->get()
                ->toArray(),
        );

        return Cinema::hydrate($cinemas);
    }

    public function isExist(string $id): bool
    {
        return Cinema::query()->where('cinemas.id', $id)->exists();
    }
}
