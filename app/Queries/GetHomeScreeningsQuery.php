<?php

namespace App\Queries;

use App\Models\Cinema;
use App\Repositories\ScreeningRepository;
use Carbon\CarbonImmutable;

class GetHomeScreeningsQuery
{
    public function __construct(private readonly ScreeningRepository $repository) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(string $cinemaId): array
    {
        $screenings = [];
        
        $collection = $this->repository->getForHomePage($cinemaId);

        foreach ($collection as $screening) {
            $screenings[] = [
                'id' => $screening->getKey(),
                'date' => $screening->starts_at->toDateString(),
                'starts_at' => $screening->starts_at->format('H:i'),
                'ends_at' => $screening->ends_at->format('H:i'),
                'status' => $screening->status->value,
                'hall' => [
                    'label' => $screening->hall->label,
                ],
                'movie' => [
                    'title' => $screening->movie->title,
                    'description' => $screening->movie->description,
                    'duration' => $screening->movie->duration,
                    'poster_url' => $screening->movie->poster_url,
                ],
            ];
        }

        return $screenings;
    }
}
