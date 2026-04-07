import { Head } from '@inertiajs/react';
import { CalendarDays, Clock3, MapPin } from 'lucide-react';
import { useState } from 'react';
import SeatHoldController from '@/actions/App/Http/Controllers/SeatHoldController';
import SeatReleaseController from '@/actions/App/Http/Controllers/SeatReleaseController';
import CinemaHall from '@/components/CinemaHall';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import client from '@/lib/client';
import type { HallRow, Seat } from '@/types';

interface ReservationPageProps {
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

export default function ReservationPage({
    screening,
    seats,
}: ReservationPageProps) {
    const [selectedSeatIds, setSelectedSeatIds] = useState<string[]>([]);

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

    const handleSeatClick = async (seat: Seat): Promise<void> => {
        if (!seat.isActive || seat.isBooked) {
            return;
        }

        if (selectedSeatIds.includes(seat.id)) {
            deselectSeat(seat.id);

            const response = await client
                .post(
                    {
                        screeningId: screening.id,
                        seatId: seat.id,
                    },
                    SeatReleaseController.url(),
                )
                .json();

            console.log(response);

            return;
        }

        selectSeat(seat.id);

        const response = await client.post({
            screeningId: screening.id,
            seatId: seat.id,
        }, SeatHoldController.url()).json();

        console.log(response);
    };

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

                <CinemaHall
                    seats={seats}
                    selectedSeatIds={selectedSeatIds}
                    onSeatClick={handleSeatClick}
                />
            </section>
        </>
    );
}
