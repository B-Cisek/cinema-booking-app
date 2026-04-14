<?php

declare(strict_types=1);

namespace App\Queries;

use App\Repositories\ScreeningRepository;
use App\Services\ScheduleDaysFactory;

readonly class GetHomeScreeningsQuery
{
    public function __construct(
        private ScreeningRepository $repository,
        private ScheduleDaysFactory $scheduleDaysFactory,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(string $cinemaId): array
    {
        $screenings = [];

        $collection = $this->repository->getForHomePage(
            $cinemaId,
            $this->scheduleDaysFactory->startsAt(),
            $this->scheduleDaysFactory->endsAt(),
        );

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
