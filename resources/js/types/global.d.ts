import type { Cinema } from '@/types';
import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            cinemas: Cinema[];
            selectedCinema: Cinema | null;
            auth: Auth;
            [key: string]: unknown;
        };
    }
}
