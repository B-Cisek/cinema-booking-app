import { Head, router } from '@inertiajs/react';
import { CalendarDays, Clock3, MapPin } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast } from 'sonner';
import type { WretchError } from 'wretch';
import SeatHoldController from '@/actions/App/Http/Controllers/SeatHoldController';
import SeatReleaseController from '@/actions/App/Http/Controllers/SeatReleaseController';
import CinemaHall from '@/components/CinemaHall';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import client from '@/lib/client';
import screeningsRoutes from '@/routes/screenings';
import type { HallRow, Seat, SharedPageProps } from '@/types';

interface ReservationPageProps extends SharedPageProps{
    seats: HallRow[];
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

interface SeatHoldResponse {
    message: string;
}

interface SeatConflictResponse {
    code?: string;
    message?: string;
}

const HOLD_DURATION_SECONDS = 300;

function formatRemainingTime(remainingSeconds: number): string {
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;

    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

export default function ReservationPage({
    screening,
    seats,
}: ReservationPageProps) {
    const [layoutSeats, setLayoutSeats] = useState<HallRow[]>(seats);
    const [selectedSeatIds, setSelectedSeatIds] = useState<string[]>([]);
    const [seatHoldExpirations, setSeatHoldExpirations] = useState<
        Record<string, number>
    >({});
    const [currentTimestamp, setCurrentTimestamp] = useState<number>(0);

    useEffect(() => {
        setLayoutSeats(seats);
    }, [seats]);

    useEffect(() => {
        const intervalId = window.setInterval(() => {
            setCurrentTimestamp(Date.now());
        }, 1000);

        return () => {
            window.clearInterval(intervalId);
        };
    }, []);

    const selectSeat = (seatId: string): void => {
        setSelectedSeatIds((current) =>
            current.includes(seatId) ? current : [...current, seatId],
        );
    };

    const deselectSeat = (seatId: string): void => {
        setSelectedSeatIds((current) =>
            current.filter((currentSeatId) => currentSeatId !== seatId),
        );
    };

    const clearSeatHold = (seatId: string): void => {
        deselectSeat(seatId);
        setSeatHoldExpirations((current) => {
            const next = { ...current };

            delete next[seatId];

            return next;
        });
    };

    const markSeatAsBooked = (seatId: string): void => {
        setLayoutSeats((current) =>
            current.map((row) => ({
                ...row,
                seats: row.seats.map((seat) =>
                    seat && seat.id === seatId
                        ? {
                              ...seat,
                              isBooked: true,
                          }
                        : seat,
                ),
            })),
        );
    };

    const handleSeatConflict = async (
        seat: Seat,
        error: WretchError,
    ): Promise<boolean> => {
        if (error.status !== 409) {
            return false;
        }

        const response = (await error.response
            .clone()
            .json()) as SeatConflictResponse;

        if (
            response.code !== 'SEAT_ALREADY_BOOKED' &&
            response.code !== 'SEAT_ALREADY_RESERVED'
        ) {
            return false;
        }

        clearSeatHold(seat.id);
        markSeatAsBooked(seat.id);
        toast.error(response.message ?? 'To miejsce jest już zajęte.');

        return true;
    };

    const handleSeatClick = async (seat: Seat): Promise<void> => {
        if (!seat.isActive || seat.isBooked) {
            return;
        }

        if (activeSelectedSeatIds.includes(seat.id)) {
            await client
                .post(
                    {
                        screeningId: screening.id,
                        seatId: seat.id,
                    },
                    SeatReleaseController.url(),
                )
                .json();

            clearSeatHold(seat.id);

            return;
        }

        try {
            await client
                .post(
                    {
                        screeningId: screening.id,
                        seatId: seat.id,
                    },
                    SeatHoldController.url(),
                )
                .json<SeatHoldResponse>();
        } catch (error) {
            if (
                error instanceof Error &&
                'status' in error &&
                'response' in error &&
                (await handleSeatConflict(seat, error as WretchError))
            ) {
                return;
            }

            throw error;
        }

        selectSeat(seat.id);
        setSeatHoldExpirations((current) => ({
            ...current,
            [seat.id]: Date.now() + HOLD_DURATION_SECONDS * 1000,
        }));
    };

    const activeExpirationTimestamps = selectedSeatIds
        .map((seatId) => seatHoldExpirations[seatId])
        .filter((expiresAt): expiresAt is number => expiresAt !== undefined)
        .filter((expiresAt) => expiresAt > currentTimestamp);
    const activeSelectedSeatIds = selectedSeatIds.filter((seatId) => {
        const expiresAt = seatHoldExpirations[seatId];

        if (!expiresAt) {
            return false;
        }

        return expiresAt > currentTimestamp;
    });
    const nearestExpirationTimestamp =
        activeExpirationTimestamps.length > 0
            ? Math.min(...activeExpirationTimestamps)
            : null;
    const remainingSeconds =
        nearestExpirationTimestamp === null
            ? null
            : Math.max(
                  0,
                  Math.ceil(
                      (nearestExpirationTimestamp - currentTimestamp) / 1000,
                  ),
              );

    return (
        <>
            <Head title={`Rezerwacja - ${screening.movie.title}`} />

            <section className="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 pb-32 sm:px-6 sm:pb-36">
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

                <CinemaHall
                    seats={layoutSeats}
                    selectedSeatIds={activeSelectedSeatIds}
                    onSeatClick={handleSeatClick}
                />
            </section>

            {remainingSeconds !== null && (
                <div className="fixed right-0 bottom-0 left-0 z-50 border-t border-border/70 bg-background/95 backdrop-blur">
                    <div className="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div className="flex min-w-0 items-center gap-3">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <Clock3 className="size-5" />
                            </div>
                            <div className="min-w-0">
                                <p className="text-sm font-semibold">
                                    Wybrane miejsca:{' '}
                                    {activeSelectedSeatIds.length}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    Rezerwacja miejsc wygasa za:{' '}
                                    {formatRemainingTime(remainingSeconds)}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center justify-between gap-3 sm:justify-end">
                            <Badge
                                variant="outline"
                                className="h-9 rounded-full px-3 text-sm"
                            >
                                <span className="mr-2 text-muted-foreground">
                                    Miejsca
                                </span>
                                {activeSelectedSeatIds.length}
                            </Badge>
                            <Button
                                size="lg"
                                className="min-w-40 rounded-full px-6"
                                onClick={() =>
                                    router.get(
                                        screeningsRoutes.reservationSummary.url(
                                            screening.id,
                                            {
                                                query: {
                                                    seatIds:
                                                        activeSelectedSeatIds,
                                                },
                                            },
                                        ),
                                    )
                                }
                            >
                                Przejdź dalej
                            </Button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
