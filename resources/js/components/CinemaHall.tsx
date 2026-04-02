import { Armchair } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { SeatMapRow, SeatMapSeat } from '@/types';

interface CinemaHallProps {
    seats: SeatMapRow[];
}

const ALL_ROWS = Array.from({ length: 12 }, (_, index) =>
    String.fromCharCode(65 + index),
);
const COLUMNS = Array.from({ length: 25 }, (_, index) => index + 1);

const seatTypeClasses: Record<SeatMapSeat['seatType'], string> = {
    standard:
        'border-border bg-card hover:border-primary/50 hover:bg-primary/5',
    vip: 'border-amber-300/70 bg-amber-100/70 hover:border-amber-400 hover:bg-amber-200/70',
    wheelchair:
        'border-sky-300/70 bg-sky-100/70 hover:border-sky-400 hover:bg-sky-200/70',
    couple: 'border-rose-300/70 bg-rose-100/70 hover:border-rose-400 hover:bg-rose-200/70',
};

function buildSeatPositionMap(
    seatRows: SeatMapRow[],
): Map<string, SeatMapSeat> {
    return new Map(
        seatRows.flatMap((row) =>
            row.seats.map(
                (seat) => [`${seat.posY}-${seat.posX}`, seat] as const,
            ),
        ),
    );
}

export default function CinemaHall({ seats }: CinemaHallProps) {
    const seatsByPosition = buildSeatPositionMap(seats);
    const visibleRows = ALL_ROWS.slice(
        0,
        Math.max(
            ...seats.flatMap((row) => row.seats.map((seat) => seat.posY)),
            0,
        ),
    );

    return (
        <Card className="gap-0 rounded-[2rem] border-border/70 shadow-lg shadow-primary/5">
            <CardHeader className="gap-3 border-b border-border/70 py-4">
                <div className="flex items-center gap-3">
                    <div className="flex size-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                        <Armchair className="size-5" />
                    </div>
                    <div>
                        <CardTitle className="text-2xl tracking-tight">
                            Wybierz miejsce
                        </CardTitle>
                    </div>
                </div>
            </CardHeader>
            <CardContent className="space-y-8 px-5 py-6 sm:px-6 sm:py-8">
                <div className="mx-auto max-w-2xl rounded-[1.75rem] border border-primary/20 bg-primary/8 px-6 py-3 text-center shadow-inner">
                    <p className="text-sm font-bold tracking-wider text-primary uppercase">
                        Ekran
                    </p>
                    <div className="mt-4 h-3 rounded-full bg-primary/50" />
                </div>
                <div className="mx-auto w-full overflow-x-auto pb-2">
                    <div className="flex min-w-max flex-col gap-2 px-1">
                        {visibleRows.map((rowLabel, rowIndex) => (
                            <div
                                key={rowLabel}
                                className="flex items-center gap-2 sm:gap-2.5"
                            >
                                <div className="flex size-8 shrink-0 items-center justify-center text-[0.7rem] font-semibold tracking-[0.22em] text-muted-foreground uppercase sm:text-xs">
                                    {rowLabel}
                                </div>
                                <div
                                    className="grid gap-1"
                                    style={{
                                        gridTemplateColumns: 'repeat(25, 2rem)',
                                    }}
                                >
                                    {COLUMNS.map((column) => {
                                        const seat = seatsByPosition.get(
                                            `${rowIndex + 1}-${column}`,
                                        );

                                        if (!seat) {
                                            return (
                                                <div
                                                    key={`${rowLabel}-${column}`}
                                                    aria-hidden="true"
                                                    className="size-8"
                                                />
                                            );
                                        }

                                        return (
                                            <button
                                                key={seat.id}
                                                type="button"
                                                disabled={
                                                    !seat.isActive ||
                                                    seat.isBooked
                                                }
                                                aria-label={`Miejsce ${seat.row}${seat.seatNumber}`}
                                                className={`flex size-8 cursor-pointer items-center justify-center rounded-md border text-xs font-semibold shadow-sm transition sm:text-sm ${seatTypeClasses[seat.seatType]} ${!seat.isActive || seat.isBooked ? 'cursor-not-allowed border-border/50 bg-muted text-muted-foreground opacity-60' : ''}`}
                                            >
                                                {seat.seatNumber}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
