<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;

class ScheduleDaysFactory
{
    private const int SCHEDULE_DAYS = 7;

    public function make(): array
    {
        $scheduleDays = [];
        $now = CarbonImmutable::now();

        for ($i = 0; $i < self::SCHEDULE_DAYS; $i++) {
            $day = $now->addDays($i)->locale(App::currentLocale());

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
