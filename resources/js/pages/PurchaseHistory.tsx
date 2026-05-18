import { Head, Link } from '@inertiajs/react';
import {
    CalendarDays,
    CreditCard,
    Clock3,
    MapPin,
    ReceiptText,
    Ticket,
} from 'lucide-react';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
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

                <div className="space-y-6">
                    {hasBookings ? (
                        <>
                            <Accordion
                                type="single"
                                collapsible
                                className="flex w-full flex-col gap-4"
                            >
                                {bookings.data.map((booking) => (
                                    <AccordionItem
                                        key={booking.id}
                                        value={booking.id}
                                        className="overflow-hidden rounded-2xl border border-border/70 bg-background shadow-sm shadow-primary/5 transition-all hover:border-primary/30 hover:shadow-md data-[state=open]:border-primary/35 data-[state=open]:shadow-md"
                                    >
                                        <AccordionTrigger className="px-4 py-4 hover:text-foreground sm:px-5">
                                            <div className="flex flex-col gap-4 text-left md:flex-row md:items-center md:justify-between">
                                                <div className="flex min-w-0 gap-4">
                                                    <img
                                                        src={
                                                            booking.screening
                                                                .movie
                                                                .poster_url
                                                        }
                                                        alt={
                                                            booking.screening
                                                                .movie.title
                                                        }
                                                        className="h-28 w-20 shrink-0 rounded-xl object-cover ring-1 ring-border/70 sm:h-32 sm:w-24"
                                                    />

                                                    <div className="flex min-w-0 flex-1 flex-col justify-between gap-4">
                                                        <div className="space-y-2">
                                                            <div className="flex flex-wrap items-center gap-2">
                                                                <Badge
                                                                    variant="secondary"
                                                                    className="h-6 px-2.5 font-semibold"
                                                                >
                                                                    {
                                                                        booking.status
                                                                    }
                                                                </Badge>
                                                                <Badge
                                                                    variant="outline"
                                                                    className="h-6 px-2.5 font-semibold"
                                                                >
                                                                    #
                                                                    {
                                                                        booking.number
                                                                    }
                                                                </Badge>
                                                            </div>

                                                            <p className="text-xl leading-tight font-semibold tracking-tight">
                                                                {
                                                                    booking
                                                                        .screening
                                                                        .movie
                                                                        .title
                                                                }
                                                            </p>
                                                        </div>

                                                        <div className="flex flex-wrap gap-2 text-sm text-muted-foreground">
                                                            <span className="inline-flex items-center gap-1.5 rounded-full bg-muted/50 px-3 py-1">
                                                                <CalendarDays className="size-3.5 text-primary" />
                                                                {
                                                                    booking
                                                                        .screening
                                                                        .date
                                                                }
                                                            </span>
                                                            <span className="inline-flex items-center gap-1.5 rounded-full bg-muted/50 px-3 py-1">
                                                                <Clock3 className="size-3.5 text-primary" />
                                                                {
                                                                    booking
                                                                        .screening
                                                                        .starts_at
                                                                }
                                                                {' - '}
                                                                {
                                                                    booking
                                                                        .screening
                                                                        .ends_at
                                                                }
                                                            </span>
                                                            <span className="inline-flex items-center gap-1.5 rounded-full bg-muted/50 px-3 py-1">
                                                                <MapPin className="size-3.5 text-primary" />
                                                                {
                                                                    booking
                                                                        .screening
                                                                        .hall
                                                                        .cinema
                                                                        .city
                                                                }
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="flex items-center justify-between gap-4 rounded-xl border border-primary/15 bg-primary/5 px-4 py-3 md:min-w-44 md:flex-col md:items-end md:text-right">
                                                    <div>
                                                        <p className="text-xs font-semibold tracking-[0.2em] text-muted-foreground uppercase">
                                                            Suma
                                                        </p>
                                                        <p className="text-xl font-semibold tracking-tight text-primary">
                                                            {formatPrice(
                                                                booking.total,
                                                            )}
                                                        </p>
                                                    </div>
                                                    <p className="text-sm font-medium text-muted-foreground">
                                                        {booking.seats.length}{' '}
                                                        biletów
                                                    </p>
                                                </div>
                                            </div>
                                        </AccordionTrigger>

                                        <AccordionContent className="space-y-5 px-4 sm:px-5">
                                            <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                                <div className="rounded-xl border border-border/70 bg-muted/20 px-4 py-3">
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

                                                <div className="rounded-xl border border-border/70 bg-muted/20 px-4 py-3">
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

                                                <div className="rounded-xl border border-border/70 bg-muted/20 px-4 py-3">
                                                    <div className="flex items-center gap-2 text-muted-foreground">
                                                        <MapPin className="size-4 text-primary" />
                                                        <span className="text-xs font-semibold tracking-[0.2em] uppercase">
                                                            Kino
                                                        </span>
                                                    </div>
                                                    <p className="mt-2 font-semibold">
                                                        {
                                                            booking.screening
                                                                .hall.label
                                                        }
                                                        ,{' '}
                                                        {
                                                            booking.screening
                                                                .hall.cinema
                                                                .city
                                                        }
                                                    </p>
                                                    <p className="text-sm text-muted-foreground">
                                                        {
                                                            booking.screening
                                                                .hall.cinema
                                                                .street
                                                        }
                                                    </p>
                                                </div>

                                                <div className="rounded-xl border border-border/70 bg-muted/20 px-4 py-3">
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
                                                            {
                                                                booking.seats
                                                                    .length
                                                            }{' '}
                                                            biletów w tym
                                                            zamówieniu
                                                        </p>
                                                    </div>
                                                </div>

                                                <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                                    {booking.seats.map(
                                                        (seat) => (
                                                            <div
                                                                key={seat.id}
                                                                className="flex items-center justify-between rounded-xl border border-border/70 bg-background px-4 py-3 shadow-xs"
                                                            >
                                                                <p className="font-semibold">
                                                                    Miejsce{' '}
                                                                    {seat.label}
                                                                </p>
                                                                <p className="text-sm font-semibold text-muted-foreground">
                                                                    {formatPrice(
                                                                        seat.price,
                                                                    )}
                                                                </p>
                                                            </div>
                                                        ),
                                                    )}
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
                </div>

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
