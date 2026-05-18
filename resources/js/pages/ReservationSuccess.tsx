import { Head, Link, usePoll } from '@inertiajs/react';
import {
    AlertCircle,
    CalendarDays,
    CheckCircle2,
    Clock3,
    LoaderCircle,
    Mail,
    MapPin,
    Ticket,
} from 'lucide-react';
import { useEffect } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { home } from '@/routes';
import type { SharedPageProps } from '@/types';

type BookingStatusCode = 'pending' | 'confirmed' | 'cancelled';

interface ReservationSuccessPageProps extends SharedPageProps {
    booking: {
        id: string;
        number: string;
        email: string;
        status: {
            code: BookingStatusCode;
            label: string;
            is_paid: boolean;
            is_pending: boolean;
        };
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

function getStatusContent(
    status: ReservationSuccessPageProps['booking']['status'],
) {
    if (status.code === 'confirmed') {
        return {
            icon: CheckCircle2,
            badge: 'Zapłacone',
            title: 'Zapłacone',
            description:
                'Płatność została potwierdzona. Szczegóły rezerwacji są dostępne poniżej.',
            tone: 'text-emerald-600 bg-emerald-500/10',
            badgeVariant: 'default' as const,
        };
    }

    if (status.code === 'cancelled') {
        return {
            icon: AlertCircle,
            badge: 'Płatność nieudana',
            title: 'Płatność nie powiodła się',
            description:
                'Nie udało się potwierdzić płatności. Rezerwacja została anulowana.',
            tone: 'text-destructive bg-destructive/10',
            badgeVariant: 'destructive' as const,
        };
    }

    return {
        icon: LoaderCircle,
        badge: 'Oczekujemy na potwierdzenie',
        title: 'Sprawdzamy status płatności',
        description:
            'Płatność jest jeszcze przetwarzana. Status odświeży się automatycznie.',
        tone: 'text-primary bg-primary/10',
        badgeVariant: 'secondary' as const,
    };
}

export default function ReservationSuccessPage({
    booking,
    screening,
}: ReservationSuccessPageProps) {
    const statusContent = getStatusContent(booking.status);
    const StatusIcon = statusContent.icon;
    const poll = usePoll(
        3000,
        {
            only: ['booking'],
        },
        {
            autoStart: booking.status.is_pending,
            keepAlive: true,
        },
    );

    useEffect(() => {
        if (!booking.status.is_pending) {
            poll.stop();
        }
    }, [booking.status.is_pending, poll]);

    return (
        <>
            <Head title={`Status płatności - ${booking.number}`} />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6">
                <Card className="overflow-hidden border-border/70 shadow-xl shadow-primary/5">
                    <CardContent>
                        <div className="flex flex-col gap-5 md:flex-row md:items-center">
                            <div
                                className={`flex size-16 items-center justify-center rounded-xl ${statusContent.tone}`}
                            >
                                <StatusIcon
                                    className={`size-8 ${booking.status.is_pending ? 'animate-spin' : ''}`}
                                />
                            </div>

                            <div className="flex-1 space-y-3">
                                <div className="space-y-2">
                                    <Badge
                                        variant={statusContent.badgeVariant}
                                        className="h-7 px-3 text-xs"
                                    >
                                        {statusContent.badge}
                                    </Badge>
                                    <h1 className="text-3xl font-semibold tracking-tight">
                                        {statusContent.title}
                                    </h1>
                                    <p className="text-sm leading-6 text-muted-foreground">
                                        {statusContent.description}
                                    </p>
                                    <p className="text-sm leading-6 text-muted-foreground">
                                        Numer rezerwacji:{' '}
                                        <strong>{booking.number}</strong> dla
                                        filmu{' '}
                                        <strong>{screening.movie.title}</strong>
                                        . Rezerwacja jest przypisana do adresu{' '}
                                        <strong>{booking.email}</strong>.
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

                {booking.status.is_pending && (
                    <div className="flex items-start gap-3 rounded-xl border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-muted-foreground">
                        <AlertCircle className="mt-0.5 size-4 shrink-0 text-primary" />
                        <p>
                            Nie zamykaj tej strony, jeśli czekasz na końcowe
                            potwierdzenie. Odświeżamy status automatycznie co
                            kilka sekund.
                        </p>
                    </div>
                )}

                <div className="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <Card className="border-border/70 shadow-lg shadow-primary/5">
                        <CardHeader className="gap-3">
                            <div className="flex items-center gap-3">
                                <div className="flex size-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Ticket className="size-5" />
                                </div>
                                <CardTitle className="text-2xl tracking-tight">
                                    Zarezerwowane miejsca
                                </CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-3">
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

                    <Card className="border-border/70 shadow-lg shadow-primary/5">
                        <CardHeader className="gap-3">
                            <div className="flex items-center gap-3">
                                <div className="flex size-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <Mail className="size-5" />
                                </div>
                                <CardTitle className="text-2xl tracking-tight">
                                    Szczegóły
                                </CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="rounded-2xl border border-border bg-muted/20 px-4 py-2">
                                <p className="text-sm text-muted-foreground">
                                    E-mail
                                </p>
                                <p className="mt-1 font-semibold">
                                    {booking.email}
                                </p>
                            </div>

                            <div className="rounded-2xl border border-border bg-muted/20 px-4 py-2">
                                <p className="text-sm text-muted-foreground">
                                    Status płatności
                                </p>
                                <p className="mt-1 font-semibold">
                                    {booking.status.label}
                                </p>
                            </div>

                            <div className="rounded-2xl border border-border bg-muted/20 px-4 py-2">
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
                                className="h-12 w-full rounded-xl text-base"
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
