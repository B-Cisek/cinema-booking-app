<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ScreeningStatus;
use App\Models\Screening;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class ScreeningRepository
{
    /** @return Collection<int, Screening> */
    public function getForHomePage(
        string $cinemaId,
        CarbonImmutable $dateRangeStart,
        CarbonImmutable $dateRangeEnd,
    ): Collection {
        return Screening::query()
            ->with([
                'hall:id,cinema_id,label',
                'movie:id,title,description,duration,poster_url',
            ])
            ->select([
                'id',
                'movie_id',
                'hall_id',
                'status',
                'starts_at',
                'ends_at',
            ])
            ->whereIn('status', [ScreeningStatus::SCHEDULED, ScreeningStatus::CANCELLED])
            ->whereHas('hall', fn ($query) => $query->where('cinema_id', $cinemaId))
            ->whereBetween('starts_at', [$dateRangeStart, $dateRangeEnd])
            ->orderBy('starts_at')
            ->get();
    }

    public function getById(string $screeningId): Screening
    {
        return Screening::query()
            ->with([
                'hall.seats',
                'bookings.bookedSeats',
            ])
            ->where('screenings.id', $screeningId)
            ->firstOrFail();
    }
}
