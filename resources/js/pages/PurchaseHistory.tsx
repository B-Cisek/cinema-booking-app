import { Head, Link } from '@inertiajs/react';
import { CalendarDays, CreditCard, MapPin, ReceiptText, Ticket } from 'lucide-react';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Pagination,
    PaginationContent,
    PaginationItem,
    PaginationLink,
} from '@/components/ui/pagination';
import { home } from '@/routes';
import type { SharedPageProps } from '@/types';

interface PurchaseHistoryPageProps extends SharedPageProps {
    bookings: {
        data: Array<{
            id: string;
            number: string;
            purchased_at: string;
            status: string;
            payment_method: string;
            total: number;
            screening: {
                id: string;
                date: string;
                starts_at: string;
                ends_at: string;
                movie: {
                    title: string;
                    poster_url: string;
                };
                hall: {
                    label: string;
                    cinema: {
                        city: string;
                        street: string;
                    };
                };
            };
            seats: Array<{
                id: string;
                label: string;
                price: number;
            }>;
        }>;
        pagination: {
            current_page: number;
            last_page: number;
            total: number;
            from: number | null;
            to: number | null;
            prev_page_url: string | null;
            next_page_url: string | null;
            links: Array<{
                page: number;
                label: string;
                url: string;
                active: boolean;
            }>;
        };
    };
}

function formatPrice(price: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
    }).format(price / 100);
}

export default function PurchaseHistoryPage({
    bookings,
}: PurchaseHistoryPageProps) {
    const hasBookings = bookings.data.length > 0;

    return (
        <>
            <Head title="Historia zakupów" />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6">
                <Card className="rounded-xl border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="flex flex-col gap-4 py-2 sm:flex-row sm:items-center sm:justify-between">
                        <div className="space-y-2">
                            <p className="text-sm font-semibold tracking-[0.22em] text-primary uppercase">
                                Twoje bilety
                            </p>
                            <div className="space-y-1">
                                <h1 className="text-3xl font-semibold tracking-tight">
                                    Historia zakupów
                                </h1>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card className="rounded-xl border-border/70 shadow-lg shadow-primary/5">
                    <CardContent className="space-y-6">
                        {hasBookings ? (
                            <>
                                <Accordion type="single" collapsible className="w-full">
                                    {bookings.data.map((booking) => (
                                        <AccordionItem
                                            key={booking.id}
                                            value={booking.id}
                                            className="last:border-b-0"
                                        >
                                            <AccordionTrigger className="py-5">
                                                <div className="grid flex-1 gap-3 text-left md:grid-cols-[minmax(0,1.7fr)_auto_auto] md:items-center">
                                                    <div className="space-y-1">
                                                        <p className="text-lg font-semibold tracking-tight">
                                                            {booking.screening.movie.title}
                                                        </p>
                                                        <p className="text-sm text-muted-foreground">
                                                            {booking.screening.date},{' '}
                                                            {booking.screening.starts_at}
                                                            {' - '}
                                                            {booking.screening.ends_at}
                                                        </p>
                                                    </div>

                                                    <div className="space-y-1 text-left md:text-right">
                                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                                            Suma
                                                        </p>
                                                        <p className="text-lg font-semibold tracking-tight">
                                                            {formatPrice(booking.total)}
                                                        </p>
                                                    </div>
                                                </div>
                                            </AccordionTrigger>

                                            <AccordionContent className="space-y-5">
                                                <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                                    <div className="rounded-2xl border border-border bg-muted/20 px-4 py-3">
                                                        <div className="flex items-center gap-2 text-muted-foreground">
                                                            <ReceiptText className="size-4 text-primary" />
                                                            <span className="text-xs font-semibold tracking-[0.2em] uppercase">
                                                                Numer
                                                            </span>
                                                        </div>
                                                        <p className="mt-2 font-semibold">
                                                            {booking.number}
                                                        </p>
                                                    </div>

                                                    <div className="rounded-2xl border border-border bg-muted/20 px-4 py-3">
                                                        <div className="flex items-center gap-2 text-muted-foreground">
                                                            <CalendarDays className="size-4 text-primary" />
                                                            <span className="text-xs font-semibold tracking-[0.2em] uppercase">
                                                                Zakupiono
                                                            </span>
                                                        </div>
                                                        <p className="mt-2 font-semibold">
                                                            {booking.purchased_at}
                                                        </p>
                                                    </div>

                                                    <div className="rounded-2xl border border-border bg-muted/20 px-4 py-3">
                                                        <div className="flex items-center gap-2 text-muted-foreground">
                                                            <MapPin className="size-4 text-primary" />
                                                            <span className="text-xs font-semibold tracking-[0.2em] uppercase">
                                                                Kino
                                                            </span>
                                                        </div>
                                                        <p className="mt-2 font-semibold">
                                                            {booking.screening.hall.label},{' '}
                                                            {booking.screening.hall.cinema.city}
                                                        </p>
                                                        <p className="text-sm text-muted-foreground">
                                                            {booking.screening.hall.cinema.street}
                                                        </p>
                                                    </div>

                                                    <div className="rounded-2xl border border-border bg-muted/20 px-4 py-3">
                                                        <div className="flex items-center gap-2 text-muted-foreground">
                                                            <CreditCard className="size-4 text-primary" />
                                                            <span className="text-xs font-semibold tracking-[0.2em] uppercase">
                                                                Płatność
                                                            </span>
                                                        </div>
                                                        <p className="mt-2 font-semibold">
                                                            {booking.payment_method}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div className="space-y-3">
                                                    <div className="flex items-center gap-2">
                                                        <div className="flex size-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                                            <Ticket className="size-4" />
                                                        </div>
                                                        <div>
                                                            <p className="font-semibold">
                                                                Miejsca
                                                            </p>
                                                            <p className="text-sm text-muted-foreground">
                                                                {booking.seats.length} biletów w tym zamówieniu
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                        {booking.seats.map((seat) => (
                                                            <div
                                                                key={seat.id}
                                                                className="flex items-center justify-between rounded-2xl border border-border bg-background px-4 py-3"
                                                            >
                                                                <p className="font-semibold">
                                                                    Miejsce {seat.label}
                                                                </p>
                                                                <p className="text-sm font-semibold text-muted-foreground">
                                                                    {formatPrice(seat.price)}
                                                                </p>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </div>
                                            </AccordionContent>
                                        </AccordionItem>
                                    ))}
                                </Accordion>
                            </>
                        ) : (
                            <div className="rounded-2xl border border-dashed border-border bg-muted/20 px-6 py-10 text-center">
                                <p className="text-lg font-semibold tracking-tight">
                                    Nie masz jeszcze opłaconych zakupów.
                                </p>
                                <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                    Gdy sfinalizujesz pierwszą rezerwację, pojawi
                                    się tutaj wraz z jej szczegółami.
                                </p>
                                <Link
                                    href={home()}
                                    className="mt-4 inline-flex text-sm font-semibold text-primary hover:underline"
                                >
                                    Przejdź do repertuaru
                                </Link>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {bookings.pagination.last_page > 1 ? (
                    <Pagination className="justify-end">
                        <PaginationContent>
                            {bookings.pagination.links.map((link) => (
                                <PaginationItem key={link.page}>
                                    <PaginationLink
                                        asChild
                                        isActive={link.active}
                                    >
                                        <Link href={link.url}>
                                            {link.label}
                                        </Link>
                                    </PaginationLink>
                                </PaginationItem>
                            ))}
                        </PaginationContent>
                    </Pagination>
                ) : null}
            </section>
        </>
    );
}
