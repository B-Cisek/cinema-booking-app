import { usePage } from '@inertiajs/react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { cn } from '@/lib/utils';
import type { Cinema } from '@/types';

export interface CinemaPickerModalLang {
    header: string;
    title: string;
    description: string;
    select: string;
    selected: string;
    empty_result: string;
    input_placeholder: string;
}

interface CinemaPickerModalProps {
    cinemas: Cinema[];
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    onSelect: (cinema: Cinema) => void;
    required?: boolean;
    search: string;
    selectedCinemaId: string | null;
    setSearch: (value: string) => void;
}

export default function CinemaPickerModal({
    cinemas,
    isOpen,
    onOpenChange,
    onSelect,
    required = false,
    search,
    selectedCinemaId,
    setSearch,
}: CinemaPickerModalProps) {
    const props = usePage().props

    const filteredCinemas = cinemas.filter((cinema) =>
        `${cinema.city} ${cinema.street}`
            .toLowerCase()
            .includes(search.trim().toLowerCase()),
    );

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent
                className="flex max-h-[85vh] max-w-2xl flex-col gap-0 overflow-hidden rounded-3xl border border-border bg-background p-0 shadow-2xl shadow-primary/10 sm:max-w-2xl"
                onEscapeKeyDown={(event) => {
                    if (required) {
                        event.preventDefault();
                    }
                }}
                onInteractOutside={(event) => {
                    if (required) {
                        event.preventDefault();
                    }
                }}
                showCloseButton={!required}
            >
                <DialogHeader className="border-b border-border px-6 py-5 text-left">
                    <p className="text-xs font-semibold tracking-[0.28em] text-primary uppercase">
                        {props.globalLang.modal.header}
                    </p>
                    <DialogTitle className="text-2xl font-semibold tracking-tight">
                        {props.globalLang.modal.title}
                    </DialogTitle>
                    <DialogDescription className="text-sm">
                        {props.globalLang.modal.description}
                    </DialogDescription>
                </DialogHeader>

                <div className="flex min-h-0 flex-1 flex-col px-6 py-5">
                    <input
                        type="text"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder={props.globalLang.modal.input_placeholder}
                        className="h-12 w-full rounded-2xl border border-input bg-background px-4 text-sm transition outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50"
                    />

                    <div className="mt-5 min-h-0 flex-1 overflow-y-auto pr-1">
                        <div className="grid gap-3">
                            {filteredCinemas.map((cinema) => {
                                const isSelected =
                                    selectedCinemaId === cinema.id;

                                return (
                                    <button
                                        key={cinema.id}
                                        type="button"
                                        onClick={() => onSelect(cinema)}
                                        className={cn(
                                            'flex items-center justify-between gap-4 rounded-2xl border px-4 py-4 text-left transition hover:border-primary/60 hover:bg-primary/5',
                                            isSelected
                                                ? 'border-primary bg-primary/8'
                                                : 'border-border bg-card',
                                        )}
                                    >
                                        <div className="space-y-1">
                                            <p className="text-base font-semibold">
                                                {cinema.city}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {cinema.street}
                                            </p>
                                        </div>

                                        <span
                                            className={cn(
                                                'rounded-full px-3 py-1 text-xs font-semibold tracking-wide',
                                                isSelected
                                                    ? 'bg-primary text-primary-foreground'
                                                    : 'bg-secondary text-secondary-foreground',
                                            )}
                                        >
                                            {isSelected
                                                ? props.globalLang.modal
                                                      .selected
                                                : props.globalLang.modal.select}
                                        </span>
                                    </button>
                                );
                            })}

                            {filteredCinemas.length === 0 ? (
                                <div className="rounded-2xl border border-dashed border-border bg-muted/40 px-4 py-8 text-center text-sm text-muted-foreground">
                                    {props.globalLang.modal.empty_result}
                                </div>
                            ) : null}
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
}
