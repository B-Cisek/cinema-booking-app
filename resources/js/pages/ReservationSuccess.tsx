import { Head, Link } from '@inertiajs/react';
import { CalendarDays, CheckCircle2, Mail, MapPin, Ticket } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { home } from '@/routes';
import { SharedPageProps } from '@/types';

interface ReservationSuccessPageProps extends SharedPageProps{
    booking: {
        id: string;
        number: string;
        email: string;
        total: number;
        seats: Array<{
            id: string;
            label: string;
            price: number;
        }>;
    };
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
            poster_url: string;
        };
    };
}

function formatPrice(price: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
    }).format(price / 100);
}

export default function ReservationSuccessPage({
    booking,
    screening,
}: ReservationSuccessPageProps) {
    return (
        <>
            <Head title={`Rezerwacja ${booking.number}`} />

            <section className="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 py-8 sm:px-6">
                <Card className="overflow-hidden rounded-[2rem] border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="px-5 py-6 sm:px-6 sm:py-8">
                        <div className="flex flex-col gap-5 md:flex-row md:items-center">
                            <div className="flex size-16 items-center justify-center rounded-3xl bg-primary/10 text-primary">
                                <CheckCircle2 className="size-8" />
                            </div>

                            <div className="flex-1 space-y-3">
                                <div className="space-y-2">
                                    <p className="text-sm font-semibold tracking-[0.22em] text-primary uppercase">
                                        Rezerwacja potwierdzona
                                    </p>
                                    <h1 className="text-3xl font-semibold tracking-tight">
                                        {screening.movie.title}
                                    </h1>
                                    <p className="text-sm leading-6 text-muted-foreground">
                                        Numer rezerwacji: {booking.number}.
                                        Potwierdzenie zostało przygotowane dla
                                        adresu {booking.email}.
                                    </p>
                                </div>

                                <div className="grid gap-3 sm:grid-cols-3">
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
                                            Seans
                                        </p>
                                        <p className="mt-1.5 font-semibold">
                                            {screening.starts_at} -{' '}
                                            {screening.ends_at}
                                        </p>
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Kino
                                        </p>
                                        <div className="mt-1.5 flex items-start gap-2">
                                            <MapPin className="mt-0.5 size-4 text-primary" />
                                            <p className="font-semibold">
                                                {screening.hall.label},{' '}
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

                <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                        <CardHeader className="gap-3">
                            <div className="flex items-center gap-3">
                                <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <Ticket className="size-5" />
                                </div>
                                <CardTitle className="text-2xl tracking-tight">
                                    Zarezerwowane miejsca
                                </CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-3 px-5 pb-6 sm:px-6">
                            {booking.seats.map((seat) => (
                                <div
                                    key={seat.id}
                                    className="flex items-center justify-between rounded-2xl border border-border bg-muted/20 px-4 py-3"
                                >
                                    <p className="font-semibold">
                                        Miejsce {seat.label}
                                    </p>
                                    <p className="text-sm font-semibold text-muted-foreground">
                                        {formatPrice(seat.price)}
                                    </p>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                        <CardHeader className="gap-3">
                            <div className="flex items-center gap-3">
                                <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <Mail className="size-5" />
                                </div>
                                <CardTitle className="text-2xl tracking-tight">
                                    Szczegóły
                                </CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4 px-5 pb-6 sm:px-6">
                            <div className="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                                <p className="text-sm text-muted-foreground">
                                    E-mail
                                </p>
                                <p className="mt-1 font-semibold">
                                    {booking.email}
                                </p>
                            </div>

                            <div className="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                                <p className="text-sm text-muted-foreground">
                                    Suma
                                </p>
                                <p className="mt-1 text-2xl font-semibold tracking-tight">
                                    {formatPrice(booking.total)}
                                </p>
                            </div>

                            <Button
                                asChild
                                size="lg"
                                className="h-12 w-full rounded-full text-base"
                            >
                                <Link href={home()}>Wróć do repertuaru</Link>
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </>
    );
}
