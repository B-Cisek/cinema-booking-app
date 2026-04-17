<?php

declare(strict_types=1);

namespace App\Support\Scheduling;

use Carbon\CarbonImmutable;
use Illuminate\Container\Attributes\Config;
use Illuminate\Foundation\Application;

readonly class ScheduleDaysFactory
{
    public function __construct(
        #[Config('app.schedule.days_range')] private int $daysRange,
        private Application $application,
    ) {}

    public function make(CarbonImmutable $startsAt): array
    {
        $scheduleDays = [];

        for ($i = 0; $i < $this->daysRange; $i++) {
            $day = $startsAt->addDays($i)->locale($this->application->currentLocale());

            $scheduleDays[] = [
                'date' => $day->toDateString(),
                'label' => $day->translatedFormat('j F'),
                'relativeLabel' => match (true) {
                    $day->isToday() => __('dates.today'),
                    $day->isTomorrow() => __('dates.tomorrow'),
                    default => ucfirst($day->translatedFormat('l')),
                },
            ];
        }

        return $scheduleDays;
    }
}
