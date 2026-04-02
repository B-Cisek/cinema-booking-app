<?php

namespace Database\Seeders;

use App\Enums\ScreeningStatus;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::all();
        $halls = Hall::all();

        if ($movies->isEmpty() || $halls->isEmpty()) {
            return;
        }

        foreach ($halls as $hallIndex => $hall) {
            $slotStarts = $this->slotStartsForHall($hallIndex, $movies);

            foreach ($movies as $movieIndex => $movie) {
                $starts = $slotStarts[$movieIndex];
                $ends = $starts->copy()->addMinutes($movie->duration);

                Screening::updateOrCreate(
                    [
                        'movie_id' => $movie->id,
                        'hall_id' => $hall->id,
                        'starts_at' => $starts,
                    ],
                    [
                        'status' => ScreeningStatus::SCHEDULED,
                        'ends_at' => $ends,
                    ],
                );
            }
        }
    }

    /**
     * @param  Collection<int, Movie>  $movies
     * @return array<int, Carbon>
     */
    private function slotStartsForHall(int $hallIndex, Collection $movies): array
    {
        $currentStart = Carbon::create(2026, 3, 30, 10, 0)->addDay($hallIndex);
        $slotStarts = [];

        foreach ($movies as $movie) {
            $slotStarts[] = $currentStart->copy();
            $currentStart = $currentStart
                ->copy()
                ->addMinutes($movie->duration + 30);
        }

        return $slotStarts;
    }
}
