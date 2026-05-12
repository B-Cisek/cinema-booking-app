import { Head, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
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
    const countdownDuration = 5;
    const [secondsRemaining, setSecondsRemaining] = useState(countdownDuration);
    const hasSubmittedRef = useRef(false);

    const handlePayment = (): void => {
        if (hasSubmittedRef.current) {
            return;
        }

        hasSubmittedRef.current = true;

        router.post(
            screeningsRoutes.completePayment.url([
                screening.id,
                booking.id,
                paymentMethod.code,
            ]),
        );
    };

    useEffect(() => {
        if (isAlreadyPaid) {
            handlePayment();

            return;
        }

        const timeoutId = window.setTimeout(() => {
            handlePayment();
        }, countdownDuration * 1000);

        const intervalId = window.setInterval(() => {
            setSecondsRemaining((currentSeconds) => {
                if (currentSeconds <= 1) {
                    window.clearInterval(intervalId);

                    return 0;
                }

                return currentSeconds - 1;
            });
        }, 1000);

        return () => {
            window.clearTimeout(timeoutId);
            window.clearInterval(intervalId);
        };
    }, [isAlreadyPaid]);

    return (
        <>
            <Head title={`Płatność - ${screening.movie.title}`} />

            <section className="relative flex min-h-screen items-center justify-center overflow-hidden bg-background px-6 py-12 text-foreground">
                <div className="pointer-events-none absolute inset-x-0 top-0 h-128 overflow-hidden">
                    <div className="absolute top-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary/15 blur-3xl" />
                    <div className="absolute top-20 right-0 h-56 w-56 rounded-full bg-primary/10 blur-3xl" />
                </div>

                <div className="relative z-10 flex max-w-3xl flex-col items-center text-center">
                    <p className="text-xs font-semibold tracking-[0.22em] text-primary uppercase">
                        Placeholder płatności
                    </p>
                    <h1 className="mt-4 text-5xl font-semibold tracking-tight sm:text-7xl">
                        {paymentMethod.label}
                    </h1>
                    <p className="mt-6 max-w-2xl text-base leading-7 text-muted-foreground sm:text-lg">
                        {paymentMethod.description}
                    </p>
                    <div className="mt-12 flex h-40 w-40 items-center justify-center rounded-full border border-primary/15 bg-primary/10 text-primary shadow-lg shadow-primary/10 sm:h-48 sm:w-48">
                        <div>
                            <p className="text-6xl font-semibold leading-none sm:text-7xl">
                                {secondsRemaining}
                            </p>
                            <p className="mt-3 text-xs font-bold tracking-[0.24em] uppercase">
                                sekund
                            </p>
                        </div>
                    </div>
                    <p className="mt-10 text-sm leading-7 text-muted-foreground sm:text-base">
                        Rezerwacja {booking.number} dla filmu {screening.movie.title}.
                        Za chwilę nastąpi przekierowanie na potwierdzenie płatności.
                    </p>
                    <p className="mt-3 text-sm font-medium text-foreground sm:text-base">
                        Kwota: {formatPrice(booking.total)}
                    </p>
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
