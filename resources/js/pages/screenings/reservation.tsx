import { Head } from '@inertiajs/react';
import { Armchair, CalendarDays, Clock3, MapPin, Ticket } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

interface ReservationPageProps {
    screening: {
        id: string;
        starts_at: string;
        ends_at: string;
        date: string;
        hall: {
            label: string;
            cinema: {
                city: string;
                street: string;
            };
        };
        movie: {
            title: string;
            description: string;
            duration: number;
            poster_url: string;
        };
    };
}

const seatRows = ['A', 'B', 'C', 'D'];
const seatNumbers = [1, 2, 3,];

export default function ReservationPage({ screening }: ReservationPageProps) {
    return (
        <>
            <Head title={`Rezerwacja - ${screening.movie.title}`} />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 sm:px-6">
                <Card className="overflow-hidden rounded-[2rem] border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="px-5 py-4 sm:px-6 sm:py-5">
                        <div className="flex flex-col gap-4 md:flex-row md:items-start">
                            <img
                                src={screening.movie.poster_url}
                                alt={screening.movie.title}
                                className="h-52 w-full rounded-[1.5rem] object-cover md:w-40"
                            />

                            <div className="flex min-w-0 flex-1 flex-col gap-4">
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge variant="outline" className="py-1">
                                        {screening.hall.label}
                                    </Badge>
                                    <Badge variant="outline" className="py-1">
                                        {screening.movie.duration} min
                                    </Badge>
                                </div>

                                <div className="space-y-2">
                                    <h1 className="text-2xl font-semibold tracking-tight sm:text-3xl">
                                        {screening.movie.title}
                                    </h1>
                                    <p className="max-w-3xl text-sm leading-6 text-muted-foreground">
                                        {screening.movie.description}
                                    </p>
                                </div>

                                <Separator />

                                <div className="grid gap-2.5 sm:grid-cols-3">
                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Data
                                        </p>
                                        <div className="mt-1.5 flex items-center gap-2">
                                            <CalendarDays className="size-4 text-primary" />
                                            <p className="font-semibold">
                                                {screening.date}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Godzina
                                        </p>
                                        <div className="mt-1.5 flex items-center gap-2">
                                            <Clock3 className="size-4 text-primary" />
                                            <p className="font-semibold">
                                                {screening.starts_at} -{' '}
                                                {screening.ends_at}
                                            </p>
                                        </div>
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Kino
                                        </p>
                                        <div className="mt-1.5 flex items-start gap-2">
                                            <MapPin className="mt-0.5 size-4 text-primary" />
                                            <p className="font-semibold">
                                                {screening.hall.cinema.city},{' '}
                                                {screening.hall.cinema.street}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                    <CardHeader className="gap-3 border-b border-border/70 pb-6">
                        <div className="flex items-center gap-3">
                            <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <Armchair className="size-5" />
                            </div>
                            <div>
                                <CardTitle className="text-2xl tracking-tight">
                                    Wybierz miejsce
                                </CardTitle>
                            </div>
                        </div>
                    </CardHeader>

                    <CardContent className="space-y-8 px-5 py-6 sm:px-6 sm:py-8">
                        <div className="mx-auto max-w-3xl rounded-[1.75rem] border border-primary/20 bg-primary/8 px-6 py-5 text-center shadow-inner">
                            <p className="text-xs font-semibold tracking-[0.35em] text-primary uppercase">
                                Ekran
                            </p>
                            <div className="mt-4 h-3 rounded-full bg-primary/50" />
                        </div>

                        <div className="mx-auto flex w-full max-w-3xl flex-col gap-4">
                            {seatRows.map((rowLabel) => (
                                <div
                                    key={rowLabel}
                                    className="grid grid-cols-[3rem_repeat(3,minmax(0,1fr))] items-center gap-3"
                                >
                                    <div className="flex h-12 items-center justify-center rounded-2xl border border-border bg-muted/40 text-sm font-semibold">
                                        {rowLabel}
                                    </div>

                                    {seatNumbers.map((seatNumber) => (
                                        <button
                                            key={`${rowLabel}-${seatNumber}`}
                                            type="button"
                                            className="aspect-square w-full max-w-14 justify-self-center rounded-2xl border border-border bg-card text-base font-semibold shadow-sm transition hover:border-primary/50 hover:bg-primary/5"
                                        >
                                            {seatNumber}
                                        </button>
                                    ))}
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </section>
        </>
    );
}
