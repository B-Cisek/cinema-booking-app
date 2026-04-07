import { Armchair } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { HallRow, Seat } from '@/types';

interface CinemaHallProps {
    seats: HallRow[];
    selectedSeatIds: string[];
    onSeatClick: (seat: Seat) => void;
}

const COLUMNS = Array.from({ length: 25 }, (_, index) => index + 1);

const seatTypeClasses: Record<Seat['seatType'], string> = {
    standard:
        'border-border bg-card hover:border-primary/50 hover:bg-primary/5',
    vip: 'border-amber-300/70 bg-amber-100/70 hover:border-amber-400 hover:bg-amber-200/70',
    wheelchair:
        'border-sky-300/70 bg-sky-100/70 hover:border-sky-400 hover:bg-sky-200/70',
    couple: 'border-rose-300/70 bg-rose-100/70 hover:border-rose-400 hover:bg-rose-200/70',
};

export default function CinemaHall({
    seats,
    selectedSeatIds,
    onSeatClick,
}: CinemaHallProps) {
    const selectedSeatIdsSet = new Set(selectedSeatIds);

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
                <p className="text-sm text-muted-foreground">
                    Kliknij miejsce, aby je zaznaczyć. Ponowne kliknięcie odznacza wybór.
                </p>
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
                        {seats.map((row) => (
                            <div
                                key={row.label}
                                className="flex items-center gap-2 sm:gap-2.5"
                            >
                                <div className="flex size-8 shrink-0 items-center justify-center text-[0.7rem] font-semibold tracking-[0.22em] text-muted-foreground uppercase sm:text-xs">
                                    {row.label}
                                </div>
                                <div
                                    className="grid gap-1"
                                    style={{
                                        gridTemplateColumns: 'repeat(25, 2rem)',
                                    }}
                                >
                                    {row.seats.map((seat, index) => {
                                        if (!seat) {
                                            return (
                                                <div
                                                    key={`${row.label}-${COLUMNS[index]}`}
                                                    aria-hidden="true"
                                                    className="size-8"
                                                />
                                            );
                                        }

                                        const isUnavailable =
                                            !seat.isActive || seat.isBooked;
                                        const isSelected =
                                            !isUnavailable && selectedSeatIdsSet.has(seat.id);
                                        const seatClasses = isUnavailable
                                            ? ''
                                            : isSelected
                                              ? 'border-primary bg-primary text-primary-foreground shadow-md shadow-primary/30 hover:border-primary hover:bg-primary hover:text-primary-foreground'
                                              : seatTypeClasses[seat.seatType];
                                        const unavailableSeatClasses =
                                            seat.isBooked
                                                ? 'cursor-not-allowed border-border/50 bg-muted opacity-70 hover:border-border/50 hover:bg-muted'
                                                : 'cursor-not-allowed border-border/50 bg-muted text-muted-foreground opacity-60';

                                        return (
                                            <button
                                                key={seat.id}
                                                type="button"
                                                disabled={isUnavailable}
                                                onClick={() =>
                                                    onSeatClick(seat)
                                                }
                                                aria-pressed={isSelected}
                                                aria-label={`Miejsce ${seat.row}${seat.seatNumber}${seat.isBooked ? ' (zarezerwowane)' : ''}`}
                                                className={`flex size-8 items-center justify-center rounded-md border text-xs font-semibold shadow-sm transition sm:text-sm ${isUnavailable ? 'cursor-not-allowed' : 'cursor-pointer'} ${seatClasses} ${isUnavailable ? unavailableSeatClasses : ''}`}
                                            >
                                                <span
                                                    aria-hidden="true"
                                                    className={
                                                        seat.isBooked
                                                            ? 'text-sm font-bold text-zinc-500 sm:text-base'
                                                            : undefined
                                                    }
                                                >
                                                    {seat.isBooked
                                                        ? 'X'
                                                        : seat.seatNumber}
                                                </span>
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
