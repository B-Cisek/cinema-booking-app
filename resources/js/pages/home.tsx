import { Head, Link } from '@inertiajs/react';
import { Clock3, Ticket } from 'lucide-react';
import { useState } from 'react';
import ScreeningReservationController from '@/actions/App/Http/Controllers/ScreeningReservationController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { Cinema, ScheduleDay, Screening } from '@/types';

interface HomeProps {
    cinemas: Cinema[];
    scheduleDays: ScheduleDay[];
    screenings: Screening[];
    selectedCinema: Cinema | null;
}

export default function Home({
    cinemas,
    scheduleDays,
    screenings,
    selectedCinema,
}: HomeProps) {
    const [activeDate, setActiveDate] = useState(scheduleDays[0]?.date ?? '');

    const screeningsForActiveDate = screenings.filter(
        (screening) => screening.date === activeDate,
    );
    const groupedScreenings = Object.values(
        screeningsForActiveDate.reduce<
            Record<
                string,
                {
                    movie: Screening['movie'];
                    screeningIds: string[];
                    hallLabels: string[];
                    times: Array<{
                        id: string;
                        startsAt: string;
                        endsAt: string;
                        hallLabel: string;
                    }>;
                }
            >
        >((groups, screening) => {
            const groupKey = screening.movie.title;

            if (!groups[groupKey]) {
                groups[groupKey] = {
                    movie: screening.movie,
                    screeningIds: [],
                    hallLabels: [screening.hall.label],
                    times: [],
                };
            }

            groups[groupKey].screeningIds.push(screening.id);

            if (!groups[groupKey].hallLabels.includes(screening.hall.label)) {
                groups[groupKey].hallLabels.push(screening.hall.label);
            }

            groups[groupKey].times.push({
                id: screening.id,
                startsAt: screening.starts_at,
                endsAt: screening.ends_at,
                hallLabel: screening.hall.label,
            });

            return groups;
        }, {}),
    );

    return (
        <>
            <Head title="Repertuar" />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 sm:px-6">
                <Card className="overflow-hidden rounded-[2rem] border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="px-4 py-4 sm:px-6 sm:py-6">
                        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-7">
                            {scheduleDays.map((day) => {
                                const isActive = day.date === activeDate;
                                const screeningsCount = screenings.filter(
                                    (screening) => screening.date === day.date,
                                ).length;

                                return (
                                    <Button
                                        key={day.date}
                                        type="button"
                                        variant={
                                            isActive ? 'default' : 'outline'
                                        }
                                        size="lg"
                                        className="h-auto min-h-24 flex-col items-start gap-1 rounded-2xl px-4 py-4 text-left"
                                        onClick={() => setActiveDate(day.date)}
                                    >
                                        <span className="text-[0.68rem] font-semibold tracking-[0.22em] uppercase opacity-80">
                                            {day.relative_label}
                                        </span>
                                        <span className="text-base font-semibold">
                                            {day.label}
                                        </span>
                                        <span className="text-xs opacity-80">
                                            {screeningsCount} seansów
                                        </span>
                                    </Button>
                                );
                            })}
                        </div>
                    </CardContent>
                </Card>

                {!selectedCinema ? (
                    <Card className="rounded-[2rem] border-dashed">
                        <CardContent className="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center">
                            <div className="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <Ticket className="size-5" />
                            </div>
                            <div className="space-y-2">
                                <p className="text-xl font-semibold">
                                    Najpierw wybierz kino
                                </p>
                                <p className="max-w-lg text-sm leading-6 text-muted-foreground">
                                    Użyj pickera w headerze, aby wskazać
                                    lokalizację i zobaczyć repertuar na kolejne
                                    7 dni.
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                ) : screeningsForActiveDate.length === 0 ? (
                    <Card className="rounded-[2rem] border-dashed">
                        <CardContent className="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center">
                            <div className="flex size-12 items-center justify-center rounded-2xl bg-secondary text-secondary-foreground">
                                <Clock3 className="size-5" />
                            </div>
                            <div className="space-y-2">
                                <p className="text-xl font-semibold">
                                    Brak seansów w tym dniu
                                </p>
                                <p className="max-w-lg text-sm leading-6 text-muted-foreground">
                                    Dla {selectedCinema.city} nie ma jeszcze
                                    zaplanowanych projekcji na wybraną datę.
                                    Sprawdź inny dzień z listy powyżej.
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {groupedScreenings.map((screeningGroup) => (
                            <Card
                                key={screeningGroup.screeningIds.join('-')}
                                className="w-full rounded-[2rem] border-border/70 shadow-lg shadow-primary/5"
                            >
                                <CardContent className="px-5 py-5 sm:px-6 sm:py-6">
                                    <div className="flex flex-col gap-5 md:flex-row md:items-start">
                                        <img
                                            src={
                                                screeningGroup.movie.poster_url
                                            }
                                            alt={screeningGroup.movie.title}
                                            className="h-56 w-full rounded-[1.5rem] object-cover md:w-40"
                                        />

                                        <div className="flex min-w-0 flex-1 flex-col gap-5">
                                            <div className="space-y-3">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <Badge
                                                        variant="secondary"
                                                        className="py-1"
                                                    >
                                                        {screeningGroup.hallLabels.join(
                                                            ', ',
                                                        )}
                                                    </Badge>
                                                    <Badge
                                                        variant="outline"
                                                        className="py-1"
                                                    >
                                                        {
                                                            screeningGroup.movie
                                                                .duration
                                                        }{' '}
                                                        min
                                                    </Badge>
                                                </div>

                                                <div className="space-y-2">
                                                    <h2 className="text-2xl font-semibold tracking-tight">
                                                        {
                                                            screeningGroup.movie
                                                                .title
                                                        }
                                                    </h2>
                                                    <p className="max-w-3xl text-sm leading-7 text-muted-foreground">
                                                        {
                                                            screeningGroup.movie
                                                                .description
                                                        }
                                                    </p>
                                                </div>
                                            </div>

                                            <Separator />

                                            <div className="flex flex-wrap gap-3">
                                                {screeningGroup.times.map(
                                                    (time) => (
                                                        <Button
                                                            key={time.id}
                                                            asChild
                                                            type="button"
                                                            variant="outline"
                                                            className="h-auto rounded-2xl px-4 py-3"
                                                        >
                                                            <Link
                                                                href={ScreeningReservationController(
                                                                    time.id,
                                                                )}
                                                                className="flex flex-col items-start"
                                                            >
                                                                <div className="flex items-center gap-2">
                                                                    <Clock3 className="size-4 text-primary" />
                                                                    <p className="text-lg font-semibold tracking-tight">
                                                                        {
                                                                            time.startsAt
                                                                        }
                                                                    </p>
                                                                </div>
                                                                <p className="mt-1 text-xs text-muted-foreground">
                                                                    do{' '}
                                                                    {
                                                                        time.endsAt
                                                                    }{' '}
                                                                    •{' '}
                                                                    {
                                                                        time.hallLabel
                                                                    }
                                                                </p>
                                                            </Link>
                                                        </Button>
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                )}
            </section>
        </>
    );
}
