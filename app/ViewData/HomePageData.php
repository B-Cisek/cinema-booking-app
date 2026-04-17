<?php

declare(strict_types=1);

namespace App\ViewData;

use App\Models\Cinema;
use App\Queries\GetHomeScreeningsQuery;
use App\Support\Scheduling\ScheduleDaysFactory;
use Carbon\CarbonImmutable;
use Illuminate\Container\Attributes\Config;

readonly class HomePageData
{
    public function __construct(
        private ScheduleDaysFactory $scheduleDaysFactory,
        #[Config('app.schedule.days_range')] private int $daysRange,
        private GetHomeScreeningsQuery $screeningsQuery,
    ) {}

    public function build(?Cinema $cinema): array
    {
        $startsAt = CarbonImmutable::now()->startOfDay();
        $endsAt = $startsAt->addDays($this->daysRange - 1)->endOfDay();

        return [
            'scheduleDays' => $this->scheduleDaysFactory->make($startsAt),
            'screenings' => $cinema
                ? $this->screeningsQuery->execute($cinema->getKey(), $startsAt, $endsAt)
                : [],
        ];
    }
}
