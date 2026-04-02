<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ScheduleDaysFactory
{
    public function make(): array
    {
        $scheduleDays = [];
        $startsAt = $this->startsAt();

        for ($i = 0; $i < $this->scheduleDays(); $i++) {
            $day = $startsAt->addDays($i)->locale(App::currentLocale());

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

    private function scheduleDays(): int
    {
        return Config::integer('app.schedule.days_range');
    }

    public function startsAt(): CarbonImmutable
    {
        return CarbonImmutable::now()->startOfDay();
    }

    public function endsAt(): CarbonImmutable
    {
        return $this->startsAt()
            ->addDays($this->scheduleDays() - 1)
            ->endOfDay();
    }
}
