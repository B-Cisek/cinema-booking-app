import type { Cinema } from '@/types';
import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            selectedCinemaId: string | null;
            cinemas: Cinema[];
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
