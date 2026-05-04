import { Head, router } from '@inertiajs/react';
import { ArrowRight, CreditCard, ShieldCheck, Ticket } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import screeningsRoutes from '@/routes/screenings';
import type { SharedPageProps } from '@/types';

interface ReservationPaymentPageProps extends SharedPageProps {
    booking: {
        id: string;
        number: string;
        email: string;
        status: string;
        total: number;
    };
    paymentMethod: {
        code: string;
        label: string;
        description: string;
    };
    screening: {
        id: string;
        starts_at: string;
        ends_at: string;
        date: string;
        movie: {
            title: string;
        };
    };
    isAlreadyPaid: boolean;
}

export default function ReservationPaymentPage({
    booking,
    paymentMethod,
    screening,
    isAlreadyPaid,
}: ReservationPaymentPageProps) {
    const handlePayment = (): void => {
        router.post(
            screeningsRoutes.completePayment.url([
                screening.id,
                booking.id,
                paymentMethod.code,
            ]),
        );
    };

    return (
        <>
            <Head title={`Płatność - ${screening.movie.title}`} />

            <section className="mx-auto flex w-full max-w-4xl flex-col gap-6 px-4 py-8 sm:px-6">
                <Card className="overflow-hidden rounded-[2rem] border-border/70 shadow-xl shadow-primary/5">
                    <CardContent className="space-y-6 px-5 py-6 sm:px-6 sm:py-8">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div className="space-y-2">
                                <p className="text-sm font-semibold tracking-[0.22em] text-primary uppercase">
                                    Testowa płatność
                                </p>
                                <h1 className="text-3xl font-semibold tracking-tight">
                                    {paymentMethod.label}
                                </h1>
                                <p className="max-w-2xl text-sm leading-6 text-muted-foreground">
                                    To jest ekran demonstracyjny bez integracji
                                    z prawdziwą bramką. Po kliknięciu przycisku
                                    poniżej rezerwacja zostanie oznaczona jako
                                    opłacona.
                                </p>
                            </div>

                            <div className="rounded-3xl border border-primary/20 bg-primary/10 px-5 py-4 text-right">
                                <p className="text-sm text-muted-foreground">
                                    Do zapłaty
                                </p>
                                <p className="text-3xl font-semibold tracking-tight">
                                    {formatPrice(booking.total)}
                                </p>
                            </div>
                        </div>

                        <div className="grid gap-4 md:grid-cols-[1.1fr_0.9fr]">
                            <div className="rounded-[1.75rem] border border-border bg-muted/20 p-5">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                        <CreditCard className="size-5" />
                                    </div>
                                    <div>
                                        <p className="font-semibold">
                                            {paymentMethod.label}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {paymentMethod.description}
                                        </p>
                                    </div>
                                </div>

                                <div className="mt-5 rounded-2xl border border-dashed border-primary/30 bg-background/80 px-4 py-4">
                                    <p className="text-sm text-muted-foreground">
                                        Symulowane przekierowanie do operatora
                                    </p>
                                    <p className="mt-1 text-lg font-semibold">
                                        {paymentMethod.label} Sandbox
                                    </p>
                                </div>
                            </div>

                            <div className="space-y-4 rounded-[1.75rem] border border-border bg-background p-5">
                                <div className="flex items-center gap-3">
                                    <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                        <Ticket className="size-5" />
                                    </div>
                                    <CardTitle className="text-2xl tracking-tight">
                                        Szczegóły
                                    </CardTitle>
                                </div>

                                <div className="space-y-3">
                                    <DetailRow
                                        label="Film"
                                        value={screening.movie.title}
                                    />
                                    <DetailRow
                                        label="Termin"
                                        value={`${screening.date}, ${screening.starts_at} - ${screening.ends_at}`}
                                    />
                                    <DetailRow
                                        label="Rezerwacja"
                                        value={booking.number}
                                    />
                                    <DetailRow
                                        label="E-mail"
                                        value={booking.email}
                                    />
                                </div>

                                <div className="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700">
                                    <div className="flex items-center gap-2">
                                        <ShieldCheck className="size-4" />
                                        <span>
                                            {isAlreadyPaid
                                                ? 'Ta rezerwacja jest już opłacona.'
                                                : 'Środowisko testowe bez realnego obciążenia karty.'}
                                        </span>
                                    </div>
                                </div>

                                <Button
                                    type="button"
                                    size="lg"
                                    className="h-12 w-full rounded-full text-base"
                                    onClick={handlePayment}
                                    disabled={isAlreadyPaid}
                                >
                                    {isAlreadyPaid ? 'Już opłacono' : 'Zapłać'}
                                    {!isAlreadyPaid && (
                                        <ArrowRight className="ml-2 size-4" />
                                    )}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </section>
        </>
    );
}

function DetailRow({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-2xl border border-border bg-muted/20 px-4 py-3">
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="mt-1 font-semibold">{value}</p>
        </div>
    );
}

function formatPrice(price: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
    }).format(price / 100);
}
