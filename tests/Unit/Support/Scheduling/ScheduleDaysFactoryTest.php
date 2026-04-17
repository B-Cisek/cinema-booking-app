<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Scheduling;

use App\Support\Scheduling\ScheduleDaysFactory;
use Carbon\CarbonImmutable;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ScheduleDaysFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $loader = new ArrayLoader;
        $loader->addMessages('pl', '*', [
            'dates.today' => 'Dzisiaj',
            'dates.tomorrow' => 'Jutro',
        ]);

        $translator = new Translator($loader, 'pl');

        $container = new Container;
        $container->instance('translator', $translator);
        Container::setInstance($container);
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        Container::setInstance();

        parent::tearDown();
    }

    #[Test]
    public function it_creates_schedule_days_for_the_configured_range_in_polish_locale(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-16 09:00:00'));

        $factory = new ScheduleDaysFactory(
            daysRange: 3,
            application: $this->makeApplicationMock(),
        );

        $startsAt = CarbonImmutable::parse('2026-04-16 00:00:00');

        $this->assertSame([
            [
                'date' => '2026-04-16',
                'label' => $startsAt->locale('pl')->translatedFormat('j F'),
                'relativeLabel' => 'Dzisiaj',
            ],
            [
                'date' => '2026-04-17',
                'label' => $startsAt->addDay()->locale('pl')->translatedFormat('j F'),
                'relativeLabel' => 'Jutro',
            ],
            [
                'date' => '2026-04-18',
                'label' => $startsAt->addDays(2)->locale('pl')->translatedFormat('j F'),
                'relativeLabel' => ucfirst($startsAt->addDays(2)->locale('pl')->translatedFormat('l')),
            ],
        ], $factory->make($startsAt));
    }

    #[Test]
    public function it_uses_the_application_locale_for_date_and_weekday_labels(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-01 09:00:00'));

        $factory = new ScheduleDaysFactory(
            daysRange: 2,
            application: $this->makeApplicationMock(),
        );

        $startsAt = CarbonImmutable::parse('2026-04-03 00:00:00');

        $this->assertSame([
            [
                'date' => '2026-04-03',
                'label' => $startsAt->locale('pl')->translatedFormat('j F'),
                'relativeLabel' => ucfirst($startsAt->locale('pl')->translatedFormat('l')),
            ],
            [
                'date' => '2026-04-04',
                'label' => $startsAt->addDay()->locale('pl')->translatedFormat('j F'),
                'relativeLabel' => ucfirst($startsAt->addDay()->locale('pl')->translatedFormat('l')),
            ],
        ], $factory->make($startsAt));
    }

    private function makeApplicationMock(): Application
    {
        $application = $this->createMock(Application::class);
        $application->method('currentLocale')->willReturn('pl');

        /** @var Application $application */
        return $application;
    }
}
