import { Head, useForm } from '@inertiajs/react';
import { CalendarDays, Clock3, Mail, MapPin, Ticket } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import screeningsRoutes from '@/routes/screenings';

interface ReservationSummaryPageProps {
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
    selectedSeats: Array<{
        id: string;
        label: string;
        row: string;
        seatNumber: number;
        seatType: string;
        price: number;
    }>;
    totalPrice: number;
}

const seatTypeLabels: Record<string, string> = {
    standard: 'Standard',
    vip: 'VIP',
    wheelchair: 'Dla osoby z niepełnosprawnością',
    couple: 'Kanapa dla dwojga',
};

export default function ReservationSummaryPage({
    screening,
    selectedSeats,
    totalPrice,
}: ReservationSummaryPageProps) {
    const form = useForm({
        email: '',
        seatIds: selectedSeats.map((seat) => seat.id),
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>): void => {
        event.preventDefault();

        form.post(screeningsRoutes.book.url(screening.id));
    };

    return (
        <>
            <Head title={`Podsumowanie - ${screening.movie.title}`} />

            <section className="mx-auto flex w-full max-w-5xl flex-col gap-6 px-4 py-8 sm:px-6">
                <Card className="overflow-hidden rounded-[2rem] border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="px-5 py-4 sm:px-6 sm:py-5">
                        <div className="flex flex-col gap-4 md:flex-row md:items-start">
                            <img
                                src={screening.movie.poster_url}
                                alt={screening.movie.title}
                                className="h-52 w-full rounded-[1.5rem] object-cover md:w-40"
                            />

                            <div className="flex min-w-0 flex-1 flex-col gap-4">
                                <div className="space-y-2">
                                    <h1 className="text-2xl font-semibold tracking-tight sm:text-3xl">
                                        Podsumowanie rezerwacji
                                    </h1>
                                    <p className="text-sm leading-6 text-muted-foreground">
                                        Sprawdź wybrane miejsca i podaj adres e-mail, na który wyślemy potwierdzenie kolejnego kroku rezerwacji.
                                    </p>
                                </div>

                                <div className="grid gap-2.5 sm:grid-cols-3">
                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Film
                                        </p>
                                        <p className="mt-1.5 font-semibold">
                                            {screening.movie.title}
                                        </p>
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Data i godzina
                                        </p>
                                        <div className="mt-1.5 space-y-1">
                                            <div className="flex items-center gap-2">
                                                <CalendarDays className="size-4 text-primary" />
                                                <p className="font-semibold">
                                                    {screening.date}
                                                </p>
                                            </div>
                                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                <Clock3 className="size-4 text-primary" />
                                                <p>
                                                    {screening.starts_at} - {screening.ends_at}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/30 px-4 py-3">
                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                            Kino
                                        </p>
                                        <div className="mt-1.5 flex items-start gap-2">
                                            <MapPin className="mt-0.5 size-4 text-primary" />
                                            <p className="font-semibold">
                                                {screening.hall.label}, {screening.hall.cinema.city}, {screening.hall.cinema.street}
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
                                <div>
                                    <CardTitle className="text-2xl tracking-tight">
                                        Wybrane miejsca
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-3 px-5 pb-6 sm:px-6">
                            {selectedSeats.map((seat) => (
                                <div
                                    key={seat.id}
                                    className="flex items-center justify-between rounded-2xl border border-border bg-muted/20 px-4 py-3"
                                >
                                    <div className="min-w-0">
                                        <p className="font-semibold">
                                            Miejsce {seat.label}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {seatTypeLabels[seat.seatType] ?? seat.seatType}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        <div className="rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
                                            Rząd {seat.row}
                                        </div>
                                        <p className="text-sm font-semibold text-muted-foreground">
                                            {formatPrice(seat.price)}
                                        </p>
                                    </div>
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
                                <div>
                                    <CardTitle className="text-2xl tracking-tight">
                                        Dane kontaktowe
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="px-5 pb-6 sm:px-6">
                            <form className="space-y-4" onSubmit={handleSubmit}>
                                <div className="space-y-2">
                                    <label
                                        htmlFor="email"
                                        className="text-sm font-semibold"
                                    >
                                        Adres e-mail
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        autoComplete="email"
                                        value={form.data.email}
                                        onChange={(event) =>
                                            form.setData(
                                                'email',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="np. jan.kowalski@example.com"
                                        className="flex h-12 w-full rounded-2xl border border-input bg-background px-4 text-sm shadow-sm outline-none transition placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20"
                                    />
                                    {form.errors.email && (
                                        <p className="text-sm text-destructive">
                                            {form.errors.email}
                                        </p>
                                    )}
                                </div>

                                <p className="text-sm leading-6 text-muted-foreground">
                                    Na ten adres zostanie wysłany bilet z potwierdzeniem rezerwacji.
                                </p>

                                <div className="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                                    <p className="text-sm text-muted-foreground">
                                        Suma
                                    </p>
                                    <p className="mt-1 text-2xl font-semibold tracking-tight">
                                        {formatPrice(totalPrice)}
                                    </p>
                                </div>

                                {form.errors.seatIds && (
                                    <p className="text-sm text-destructive">
                                        {form.errors.seatIds}
                                    </p>
                                )}

                                <Button
                                    type="submit"
                                    size="lg"
                                    className="h-12 w-full rounded-full text-base"
                                    disabled={form.processing}
                                >
                                    {form.processing ? 'Rezerwowanie...' : 'Rezerwuj'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </>
    );
}

function formatPrice(price: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
    }).format(price / 100);
}
