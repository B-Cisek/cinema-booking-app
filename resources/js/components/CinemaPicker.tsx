import { router, usePage } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import { useState } from 'react';
import CinemaPickerModal from '@/components/CinemaPickerModal';
import cinemasRoutes from '@/routes/cinemas';
import type { Cinema } from '@/types';

export default function CinemaPicker() {
    const { selectedCinemaId, cinemas } = usePage().props;

    const [isCinemaModalOpen, setIsCinemaModalOpen] = useState(false);
    const [search, setSearch] = useState('');

    const selectedCinema =
        cinemas.find((cinema) => cinema.id === selectedCinemaId) ?? null;

    const handleSelectCinema = (cinema: Cinema): void => {
        router.post(
            cinemasRoutes.select(),
            { id: cinema.id },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsCinemaModalOpen(false);
                    setSearch('');
                },
            },
        );
    };

    const handleCinemaModalOpenChange = (open: boolean): void => {
        setIsCinemaModalOpen(open);
        setSearch('');
    };

    return (
        <>
            <button
                type="button"
                onClick={() => setIsCinemaModalOpen(true)}
                className="flex min-w-0 items-center gap-3 rounded-2xl border border-border bg-card px-3 py-2 text-left transition hover:border-primary/50 hover:bg-primary/5 sm:px-4"
            >
                <div className="flex size-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <MapPin className="size-4" />
                </div>

                <div className="hidden min-w-0 sm:block">
                    <p className="truncate text-sm font-semibold">
                        {selectedCinema
                            ? `${selectedCinema.city}, ${selectedCinema.street}`
                            : 'Wybierz lokalizację'}
                    </p>
                </div>

                <div className="sm:hidden">
                    <p className="text-sm font-semibold">
                        {selectedCinema ? selectedCinema.city : 'Wybierz kino'}
                    </p>
                </div>
            </button>

            <CinemaPickerModal
                cinemas={cinemas}
                isOpen={isCinemaModalOpen}
                onOpenChange={handleCinemaModalOpenChange}
                onSelect={handleSelectCinema}
                search={search}
                selectedCinemaId={selectedCinemaId}
                setSearch={setSearch}
            />
        </>
    );
}
