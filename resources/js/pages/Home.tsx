import { Head, Link } from '@inertiajs/react';
import { Clock3, Ticket } from 'lucide-react';
import { useState } from 'react';
import ScreeningReservationController from '@/actions/App/Http/Controllers/ScreeningReservationController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { ScheduleDay, Screening, SharedPageProps } from '@/types';

interface HomeProps extends SharedPageProps {
    scheduleDays: ScheduleDay[];
    screenings: Screening[];
}

export default function Home({
    scheduleDays,
    screenings,
    selectedCinema,
}: HomeProps) {
    const [activeDate, setActiveDate] = useState(scheduleDays[0]?.date ?? '');

    const screeningsForSelectedDate = screenings.filter(
        (screening) => screening.date === activeDate,
    );

    const groups: Record<
        string,
        Array<{
            id: string;
            startsAt: string;
            endsAt: string;
            hallLabel: string;
            movie: {
                id: string;
                title: string;
                description: string;
                duration: number;
                poster_url: string;
            };
        }>
    > = {};

    for (const screening of screeningsForSelectedDate) {
        const movieId = screening.movie.id;

        groups[movieId] ??= [];

        groups[movieId].push({
            id: screening.id,
            startsAt: screening.starts_at,
            endsAt: screening.ends_at,
            hallLabel: screening.hall.label,
            movie: {
                id: screening.movie.id,
                title: screening.movie.title,
                description: screening.movie.description,
                duration: screening.movie.duration,
                poster_url: screening.movie.poster_url,
            },
        });
    }

    const groupedScreenings = Object.values(groups);

    return (
        <>
            <Head title="Repertuar" />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 sm:px-6">
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
                                variant={isActive ? 'default' : 'outline'}
                                size="lg"
                                className="h-auto min-h-24 cursor-pointer flex-col items-start gap-1 rounded-xl px-4 py-4 text-left"
                                onClick={() => setActiveDate(day.date)}
                            >
                                <span className="text-[0.68rem] font-semibold tracking-[0.22em] uppercase opacity-80">
                                    {day.relative_label}
                                </span>
                                <span className="text-base font-semibold">
                                    {day.label}
                                </span>
                                <span className="text-xs opacity-80">
                                    Seanse ({screeningsCount})
                                </span>
                            </Button>
                        );
                    })}
                </div>


                {!selectedCinema ? (
                    <Card className="border-dashed">
                        <CardContent className="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center">
                            <div className="flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                <Ticket className="size-5" />
                            </div>
                            <div className="space-y-2">
                                <p className="text-xl font-semibold">
                                    Wybierz kino
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                ) : screeningsForSelectedDate.length === 0 ? (
                    <Card className="border-dashed">
                        <CardContent className="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center">
                            <div className="flex size-12 items-center justify-center rounded-xl bg-secondary text-secondary-foreground">
                                <Clock3 className="size-5" />
                            </div>
                            <div className="space-y-2">
                                <p className="text-xl font-semibold">
                                    Brak seansów w tym dniu
                                </p>
                            </div>
                        </CardContent>
                    </Card>
                ) : (
                    <div className="space-y-4">
                        {groupedScreenings.map((screeningGroup) => {
                            const movie = screeningGroup[0].movie;

                            return (
                                <Card
                                    key={movie.title}
                                    className="w-full border-border/70 shadow-lg shadow-primary/5"
                                >
                                    <CardContent>
                                        <div className="flex flex-col gap-5 md:flex-row md:items-start">
                                            <img
                                                src={movie.poster_url}
                                                alt={movie.title}
                                                className="h-56 w-full rounded-xl object-cover md:w-40"
                                            />

                                            <div className="flex min-w-0 flex-1 flex-col gap-5">
                                                <div className="space-y-3">
                                                    <div className="flex flex-wrap items-center gap-2">
                                                        <Badge className="text-xs">
                                                            {movie.duration} min
                                                        </Badge>
                                                    </div>

                                                    <div className="space-y-2">
                                                        <h2 className="text-2xl font-semibold tracking-tight">
                                                            {movie.title}
                                                        </h2>
                                                        <p className="max-w-3xl text-sm leading-7 text-muted-foreground">
                                                            {movie.description}
                                                        </p>
                                                    </div>
                                                </div>

                                                <Separator />

                                                <div className="flex flex-wrap gap-3">
                                                    {screeningGroup.map(
                                                        (screening) => (
                                                            <Button
                                                                key={`${screening.movie.title}-${screening.startsAt}-${screening.endsAt}`}
                                                                asChild
                                                                type="button"
                                                                variant="outline"
                                                                className="h-auto px-3 py-2"
                                                            >
                                                                <Link
                                                                    href={ScreeningReservationController(
                                                                        screening.id,
                                                                    )}
                                                                    className="flex flex-col items-start"
                                                                >
                                                                    <div className="flex items-center gap-2">
                                                                        <Clock3 className="size-4 text-primary" />
                                                                        <p className="text-lg font-semibold tracking-tight">
                                                                            {
                                                                                screening.startsAt
                                                                            }
                                                                        </p>
                                                                    </div>
                                                                    <p className="mt-1 text-xs text-muted-foreground">
                                                                        do {screening.endsAt} | {screening.hallLabel}
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
                            );
                        })}
                    </div>
                )}
            </section>
        </>
    );
}
