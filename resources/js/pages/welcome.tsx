import { Head } from '@inertiajs/react';
import { CalendarDays, Clock3, Film, MapPin } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

const screeningDates = [
    { day: 'Today', label: 'Mar 27', active: true },
    { day: 'Fri', label: 'Mar 28' },
    { day: 'Sat', label: 'Mar 29' },
    { day: 'Sun', label: 'Mar 30' },
];

const movies = [
    {
        title: 'Dune: Part Two',
        genre: 'Sci-Fi',
        runtime: '166 min',
        hall: 'Hall 1',
        description:
            'A large-format desert epic with late evening screenings and premium sound.',
        times: ['14:10', '17:30', '20:45'],
    },
    {
        title: 'Past Lives',
        genre: 'Drama',
        runtime: '106 min',
        hall: 'Hall 2',
        description:
            'An intimate character-driven story scheduled for early and mid-evening slots.',
        times: ['13:20', '16:00', '18:40'],
    },
    {
        title: 'Spider-Man: Across the Spider-Verse',
        genre: 'Animation',
        runtime: '140 min',
        hall: 'Hall 3',
        description:
            'A colorful, fast-paced family favorite with the most accessible afternoon times.',
        times: ['12:00', '15:15', '19:10'],
    },
];

export default function Welcome() {
    return (
        <>
            <Head title="Cinema Repertoire" />

            <main className="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.22),_transparent_30%),linear-gradient(180deg,_#07110f,_#0b1715_42%,_#050807)] text-white">
                <div className="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
                    <section className="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-8">
                        <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div className="max-w-2xl space-y-4">
                                <Badge
                                    variant="secondary"
                                    className="gap-1.5 rounded-full border-white/10 bg-white/10 px-3 py-1 text-white"
                                >
                                    <Film className="size-3.5" />
                                    City Cinema
                                </Badge>
                                <div className="space-y-3">
                                    <h1 className="font-heading text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                                        Today&apos;s repertoire, ready for
                                        booking.
                                    </h1>
                                    <p className="max-w-xl text-sm leading-6 text-white/70 sm:text-base">
                                        Browse the day&apos;s screenings,
                                        compare hours, and pick the movie that
                                        fits your evening.
                                    </p>
                                </div>
                            </div>

                            <div className="grid gap-3 sm:grid-cols-2">
                                <div className="rounded-2xl border border-white/10 bg-black/20 px-4 py-3">
                                    <div className="flex items-center gap-2 text-sm font-medium text-white">
                                        <CalendarDays className="size-4 text-primary" />
                                        Thursday, March 27
                                    </div>
                                    <p className="mt-1 text-sm text-white/65">
                                        9 screenings across 3 halls
                                    </p>
                                </div>
                                <div className="rounded-2xl border border-white/10 bg-black/20 px-4 py-3">
                                    <div className="flex items-center gap-2 text-sm font-medium text-white">
                                        <MapPin className="size-4 text-primary" />
                                        Old Town, Main Street 18
                                    </div>
                                    <p className="mt-1 text-sm text-white/65">
                                        Standard and premium rooms available
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="flex flex-col gap-3">
                        <div className="flex items-center justify-between">
                            <div>
                                <h2 className="text-lg font-semibold tracking-tight text-white">
                                    Select date
                                </h2>
                                <p className="text-sm text-white/65">
                                    Repertoire updates daily at 08:00.
                                </p>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            {screeningDates.map((date) => (
                                <button
                                    key={date.label}
                                    type="button"
                                    className={`rounded-2xl border px-4 py-3 text-left transition-colors ${
                                        date.active
                                            ? 'border-primary/40 bg-primary/18 text-white shadow-lg shadow-primary/10'
                                            : 'border-white/10 bg-white/5 text-white/85 hover:bg-white/10'
                                    }`}
                                >
                                    <div className="text-sm font-medium">
                                        {date.day}
                                    </div>
                                    <div className="text-sm text-white/60">
                                        {date.label}
                                    </div>
                                </button>
                            ))}
                        </div>
                    </section>

                    <section className="grid gap-5 lg:grid-cols-3">
                        {movies.map((movie) => (
                            <Card
                                key={movie.title}
                                className="overflow-hidden rounded-[1.75rem] border-white/10 bg-white/6 shadow-xl shadow-black/20"
                            >
                                <CardHeader className="gap-4">
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="space-y-2">
                                            <CardTitle className="text-xl text-white">
                                                {movie.title}
                                            </CardTitle>
                                            <div className="flex flex-wrap gap-2">
                                                <Badge
                                                    variant="outline"
                                                    className="border-white/12 bg-white/4 text-white/85"
                                                >
                                                    {movie.genre}
                                                </Badge>
                                                <Badge
                                                    variant="secondary"
                                                    className="border-white/10 bg-white/10 text-white"
                                                >
                                                    {movie.hall}
                                                </Badge>
                                            </div>
                                        </div>
                                        <div className="rounded-full bg-primary/15 p-2 text-primary">
                                            <Film className="size-4" />
                                        </div>
                                    </div>
                                    <CardDescription className="leading-6 text-white/65">
                                        {movie.description}
                                    </CardDescription>
                                </CardHeader>

                                <CardContent className="space-y-5">
                                    <div className="flex items-center gap-2 text-sm text-white/65">
                                        <Clock3 className="size-4" />
                                        Runtime: {movie.runtime}
                                    </div>

                                    <Separator className="bg-white/8" />

                                    <div className="space-y-3">
                                        <p className="text-sm font-medium text-white">
                                            Showtimes
                                        </p>
                                        <div className="flex flex-wrap gap-2">
                                            {movie.times.map((time) => (
                                                <Badge
                                                    key={time}
                                                    variant="outline"
                                                    className="rounded-full border-white/12 bg-black/20 px-3 py-1 text-sm text-white"
                                                >
                                                    {time}
                                                </Badge>
                                            ))}
                                        </div>
                                    </div>
                                </CardContent>

                                <CardFooter className="mt-auto pt-1">
                                    <Button
                                        size="lg"
                                        className="w-full rounded-full bg-primary text-primary-foreground hover:bg-primary/85"
                                    >
                                        Book seats
                                    </Button>
                                </CardFooter>
                            </Card>
                        ))}
                    </section>
                </div>
            </main>
        </>
    );
}
