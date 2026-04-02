import { usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import AppHeader from '@/components/AppHeader';
import type { User } from '@/types';

interface SharedPageProps {
    [key: string]: unknown;
    auth?: {
        user?: User | null;
    };
}

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth } = usePage<SharedPageProps>().props;

    return (
        <div className="min-h-screen bg-background text-foreground">
            <div className="pointer-events-none absolute inset-x-0 top-0 -z-10 h-128 overflow-hidden">
                <div className="absolute top-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary/15 blur-3xl" />
                <div className="absolute top-20 right-0 h-56 w-56 rounded-full bg-primary/10 blur-3xl" />
            </div>

            <AppHeader auth={auth} />

            <main>{children}</main>
        </div>
    );
}
