import { router, usePage } from '@inertiajs/react';
import { MapPin } from 'lucide-react';
import { useState } from 'react';
import CinemaPickerModal from '@/components/CinemaPickerModal';
import cinemasRoutes from '@/routes/cinemas';
import type { Cinema, SharedPageProps } from '@/types';

export default function CinemaPicker() {
    const { url } = usePage();
    const isHome = url === '/';

    const { selectedCinema, cinemas, globalLang } =
        usePage<SharedPageProps>().props;

    const [isCinemaModalOpen, setIsCinemaModalOpen] = useState(
        () => isHome && selectedCinema === null,
    );
    const [search, setSearch] = useState('');

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
                className="flex h-10 min-w-0 cursor-pointer items-center gap-2 rounded-xl border border-border bg-card px-2.5 text-left transition hover:border-primary/50 hover:bg-primary/5 sm:px-3"
            >
                <div className="flex size-6 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <MapPin className="size-4" />
                </div>

                <div className="hidden min-w-0 sm:block">
                    <p className="truncate text-xs font-semibold">
                        {selectedCinema
                            ? `${selectedCinema.city}, ${selectedCinema.street}`
                            : globalLang.button.cinema_picker}
                    </p>
                </div>

                <div className="sm:hidden">
                    <p className="text-xs font-semibold">
                        {selectedCinema
                            ? selectedCinema.city
                            : globalLang.button.cinema_picker}
                    </p>
                </div>
            </button>

            <CinemaPickerModal
                required={isHome && selectedCinema === null}
                cinemas={cinemas}
                isOpen={isCinemaModalOpen}
                onOpenChange={handleCinemaModalOpenChange}
                onSelect={handleSelectCinema}
                search={search}
                selectedCinemaId={selectedCinema?.id ?? null}
                setSearch={setSearch}
            />
        </>
    );
}
