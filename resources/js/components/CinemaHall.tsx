import { Armchair } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

export default function CinemaHall() {
    const rows = Array.from({ length: 12 }, (_, index) =>
        String.fromCharCode(65 + index),
    );
    const seats = Array.from({ length: 25 }, (_, index) => index + 1);

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
                    <div className="flex items-center flex-col gap-2">
                        {rows.map((row) => (
                            <div key={row} className="flex items-center gap-2 sm:gap-2.5">
                                <div className="flex size-8 shrink-0 items-center justify-center text-[0.7rem] font-semibold tracking-[0.22em] text-muted-foreground uppercase sm:text-xs">
                                    {row}
                                </div>
                                <div
                                    className="grid gap-1"
                                    style={{
                                        gridTemplateColumns: 'repeat(25, 2rem)',
                                    }}
                                >
                                    {seats.map((seatNumber) => (
                                        <button
                                            key={`${row}-${seatNumber}`}
                                            type="button"
                                            className="flex size-8 items-center justify-center cursor-pointer rounded-md border border-border bg-card text-xs font-semibold shadow-sm transition hover:border-primary/50 hover:bg-primary/5 sm:text-sm"
                                        >
                                            {seatNumber}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
