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
        $cinemas = Cache::rememberForever(
            self::CINEMAS_KEY,
            fn () => Cinema::query()
                ->select('cinemas.id', 'cinemas.street', 'cinemas.city')
                ->get()
                ->toArray()
        );

        return Cinema::hydrate($cinemas);
    }

    public function isExist(string $id): bool
    {
        return Cinema::query()->where('cinemas.id', $id)->exists();
    }

    public function getById(string $id): ?Cinema
    {
        return Cinema::query()->select('cinemas.id', 'cinemas.street', 'cinemas.city')->find($id);
    }
}
