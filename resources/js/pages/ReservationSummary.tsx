import { Head, useForm } from '@inertiajs/react';
import {
    CalendarDays,
    CircleDollarSign,
    Clock3,
    CreditCard,
    Mail,
    MapPin,
    Ticket,
} from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import screeningsRoutes from '@/routes/screenings';
import type { SharedPageProps } from '@/types';

interface ReservationSummaryPageProps extends SharedPageProps{
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
    paymentMethods: Array<{
        code: string;
        label: string;
        description: string;
    }>;
}

const seatTypeLabels: Record<string, string> = {
    standard: 'Standard',
    vip: 'VIP',
    wheelchair: 'Dla osoby z niepełnosprawnością',
    couple: 'Kanapa dla dwojga',
};

export default function ReservationSummaryPage({
    auth,
    screening,
    selectedSeats,
    totalPrice,
    paymentMethods,
}: ReservationSummaryPageProps) {
    const loggedInUserEmail = auth.user?.email ?? null;
    const form = useForm({
        email: loggedInUserEmail ?? '',
        paymentMethod: paymentMethods[0]?.code ?? '',
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
                                        Sprawdź wybrane miejsca i wybierz metodę
                                        płatności. {loggedInUserEmail
                                            ? 'Po zaksięgowaniu testowej płatności wyślemy bilet na adres przypisany do Twojego konta.'
                                            : 'Podaj adres e-mail, na który wyślemy potwierdzenie kolejnego kroku rezerwacji.'}
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
                                                    {screening.starts_at} -{' '}
                                                    {screening.ends_at}
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

                <form
                    className="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]"
                    onSubmit={handleSubmit}
                >
                    <div className="space-y-6">
                        {!loggedInUserEmail && (
                            <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                                <CardHeader className="gap-3">
                                    <div className="flex items-center gap-3">
                                        <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <Mail className="size-5" />
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold tracking-[0.22em] text-muted-foreground uppercase">
                                                Twoje dane
                                            </p>
                                            <CardTitle className="text-2xl tracking-tight">
                                                Kontakt do wysyłki biletu
                                            </CardTitle>
                                        </div>
                                    </div>
                                </CardHeader>
                                <CardContent className="space-y-4 px-5 pb-6 sm:px-6">
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
                                            className="flex h-12 w-full rounded-2xl border border-input bg-background px-4 text-sm shadow-sm transition outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/20"
                                        />
                                        {form.errors.email && (
                                            <p className="text-sm text-destructive">
                                                {form.errors.email}
                                            </p>
                                        )}
                                    </div>

                                    <div className="rounded-2xl border border-border bg-muted/20 px-4 py-4">
                                        <p className="text-sm leading-6 text-muted-foreground">
                                            Na ten adres wyślemy bilet po
                                            zaksięgowaniu testowej płatności.
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                            <CardHeader className="gap-3">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                        <CreditCard className="size-5" />
                                    </div>
                                    <div>
                                        <p className="text-xs font-semibold tracking-[0.22em] text-muted-foreground uppercase">
                                            Metoda płatności
                                        </p>
                                        <CardTitle className="text-2xl tracking-tight">
                                            Wybierz bramkę
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-3 px-5 pb-6 sm:px-6">
                                {paymentMethods.map((paymentMethod) => (
                                    <label
                                        key={paymentMethod.code}
                                        className="flex cursor-pointer items-start gap-3 rounded-2xl border border-border bg-muted/20 px-4 py-4 transition hover:border-primary/40"
                                    >
                                        <input
                                            type="radio"
                                            name="paymentMethod"
                                            value={paymentMethod.code}
                                            checked={
                                                form.data.paymentMethod ===
                                                paymentMethod.code
                                            }
                                            onChange={(event) =>
                                                form.setData(
                                                    'paymentMethod',
                                                    event.target.value,
                                                )
                                            }
                                            className="mt-1 size-4 border-border text-primary focus:ring-primary"
                                        />
                                        <div className="space-y-1">
                                            <p className="font-semibold">
                                                {paymentMethod.label}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {paymentMethod.description}
                                            </p>
                                        </div>
                                    </label>
                                ))}

                                {form.errors.paymentMethod && (
                                    <p className="text-sm text-destructive">
                                        {form.errors.paymentMethod}
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card className="rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
                        <CardHeader className="gap-3">
                            <div className="flex items-center gap-3">
                                <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <Ticket className="size-5" />
                                </div>
                                <div>
                                    <p className="text-xs font-semibold tracking-[0.22em] text-muted-foreground uppercase">
                                        Twój koszyk
                                    </p>
                                    <CardTitle className="text-2xl tracking-tight">
                                        Bilety i płatność
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4 px-5 pb-6 sm:px-6">
                            <div className="space-y-3">
                                {selectedSeats.map((seat) => (
                                    <div
                                        key={seat.id}
                                        className="rounded-2xl border border-border bg-muted/20 px-4 py-4"
                                    >
                                        <div className="flex items-start justify-between gap-4">
                                            <div className="min-w-0 space-y-1">
                                                <p className="font-semibold">
                                                    Bilet: miejsce {seat.label}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {seatTypeLabels[
                                                        seat.seatType
                                                    ] ?? seat.seatType}
                                                </p>
                                            </div>
                                            <p className="shrink-0 font-semibold">
                                                {formatPrice(seat.price)}
                                            </p>
                                        </div>

                                        <div className="mt-3 flex items-center gap-2 text-sm text-primary">
                                            <span className="rounded-full bg-primary/10 px-3 py-1 font-semibold">
                                                Rząd {seat.row}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="rounded-[1.75rem] border border-primary/15 bg-primary/5 px-5 py-5">
                                <div className="flex items-center justify-between gap-4">
                                    <div className="flex items-center gap-3">
                                        <div className="flex size-10 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <CircleDollarSign className="size-5" />
                                        </div>
                                        <div>
                                            <p className="text-sm text-muted-foreground">
                                                Suma do zapłaty
                                            </p>
                                            <p className="text-2xl font-semibold tracking-tight">
                                                {formatPrice(totalPrice)}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-2xl border border-amber-500/25 bg-amber-500/10 px-4 py-4">
                                <p className="text-sm leading-6 text-amber-900">
                                    Klikając przycisk poniżej, kupujesz z
                                    obowiązkiem zapłaty i przechodzisz do
                                    testowej strony wybranej bramki.
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
                                {form.processing
                                    ? 'Przekierowanie...'
                                    : 'Kupuję i płacę'}
                            </Button>
                        </CardContent>
                    </Card>
                </form>
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
