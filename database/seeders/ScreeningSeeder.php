<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ScreeningStatus;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::query()->where('is_active', true)->get();
        $cinemas = Cinema::query()->with('halls')->get();

        if ($movies->isEmpty() || $cinemas->isEmpty()) {
            return;
        }

        foreach ($cinemas as $cinemaIndex => $cinema) {
            $halls = $cinema->halls->sortBy('name')->values();

            if ($halls->isEmpty()) {
                continue;
            }

            foreach (range(0, $this->screeningDays() - 1) as $dayOffset) {
                $movieSchedule = $this->movieScheduleForDay($movies, $dayOffset + $cinemaIndex);
                $screeningPlan = array_slice(
                    $this->screeningPlanForDay($halls),
                    0,
                    count($movieSchedule),
                );
                $desiredScreeningKeys = [];

                foreach ($screeningPlan as $planIndex => $plan) {
                    $movie = $movieSchedule[$planIndex];
                    $startsAt = $this->startsAtForSlot($dayOffset, $plan['slot']);

                    Screening::query()->updateOrCreate(
                        [
                            'hall_id' => $plan['hall']->getKey(),
                            'starts_at' => $startsAt,
                        ],
                        [
                            'movie_id' => $movie->getKey(),
                            'status' => $this->statusFor($dayOffset, $planIndex),
                            'ends_at' => $startsAt->addMinutes($movie->duration),
                        ],
                    );

                    $desiredScreeningKeys[] = $this->screeningKey(
                        $plan['hall']->getKey(),
                        $startsAt,
                    );
                }

                $this->deleteObsoleteScreeningsForDay(
                    $cinema->getKey(),
                    $this->startsAtForDay($dayOffset),
                    $desiredScreeningKeys,
                );
            }
        }
    }

    /**
     * @param  Collection<int, Hall>  $halls
     * @return array<int, array{hall: Hall, slot: array{hour: int, minute: int}}>
     */
    private function screeningPlanForDay(Collection $halls): array
    {
        $plan = [];

        foreach ($halls as $hall) {
            foreach ($this->dailySlots() as $slotStart) {
                $plan[] = [
                    'hall' => $hall,
                    'slot' => $slotStart,
                ];
            }
        }

        return $plan;
    }

    /**
     * @param  Collection<int, Movie>  $movies
     * @return array<int, Movie>
     */
    private function movieScheduleForDay(Collection $movies, int $rotation): array
    {
        $rotation = $rotation % $movies->count();

        $rotatedMovies = $movies
            ->values()
            ->slice($rotation)
            ->concat($movies->values()->take($rotation))
            ->values();

        return collect([
            ...$rotatedMovies->all(),
            ...$rotatedMovies->all(),
        ])->values()->all();
    }

    /**
     * @return array<int, array{hour: int, minute: int}>
     */
    private function dailySlots(): array
    {
        return [
            ['hour' => 10, 'minute' => 0],
            ['hour' => 13, 'minute' => 15],
            ['hour' => 16, 'minute' => 45],
            ['hour' => 20, 'minute' => 0],
        ];
    }

    /**
     * @param  array{hour: int, minute: int}  $slotStart
     */
    private function startsAtForSlot(int $dayOffset, array $slotStart): CarbonImmutable
    {
        return $this->startsAtForDay($dayOffset)
            ->setTime($slotStart['hour'], $slotStart['minute']);
    }

    private function startsAtForDay(int $dayOffset): CarbonImmutable
    {
        return CarbonImmutable::now()
            ->startOfDay()
            ->addDays($dayOffset);
    }

    private function screeningDays(): int
    {
        return (int) $this->startsAt()->diffInDays($this->endsAt()) + 1;
    }

    private function statusFor(int $dayOffset, int $planIndex): ScreeningStatus
    {
        if (($dayOffset + $planIndex) % 11 === 0 && $planIndex % count($this->dailySlots()) === 3) {
            return ScreeningStatus::CANCELLED;
        }

        return ScreeningStatus::SCHEDULED;
    }

    /**
     * @param  array<int, string>  $desiredScreeningKeys
     */
    private function deleteObsoleteScreeningsForDay(
        string $cinemaId,
        CarbonImmutable $date,
        array $desiredScreeningKeys,
    ): void {
        Screening::query()
            ->whereDate('starts_at', $date->toDateString())
            ->whereHas('hall', fn ($query) => $query->where('cinema_id', $cinemaId))
            ->get()
            ->reject(fn (Screening $screening): bool => in_array(
                $this->screeningKey($screening->hall_id, $screening->starts_at),
                $desiredScreeningKeys,
                true,
            ))
            ->each
            ->delete();
    }

    private function screeningKey(string $hallId, CarbonImmutable $startsAt): string
    {
        return sprintf('%s|%s', $hallId, $startsAt->toDateTimeString());
    }

    private function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::now()->startOfDay();
    }

    private function endsAt(): CarbonImmutable
    {
        return $this->startsAt()
            ->addMonthsNoOverflow(6)
            ->endOfDay();
    }
}
