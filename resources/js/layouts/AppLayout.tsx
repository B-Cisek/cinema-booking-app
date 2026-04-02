import { usePage } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import type { PropsWithChildren} from 'react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import AppHeader from '@/components/AppHeader';
import { Toaster } from '@/components/ui/sonner';

 interface FlashMessages {
    success?: string;
    error?: string;
    info?: string;
    warning?: string;
    message?: string;
}

const flashHandlers = {
    success: toast.success,
    error: toast.error,
    info: toast.info,
    warning: toast.warning,
    message: toast,
} as const;

export default function AppLayout({ children }: PropsWithChildren) {
    const { auth } = usePage().props

    useEffect(() => {
        const removeListener = router.on('flash', (event) => {
            const flash  = event.detail.flash as FlashMessages;
            const entries = Object.entries(flash) as Array<[keyof typeof flashHandlers, string]>;

            if (entries.length === 0) {
                return;
            }

            for (const [level, message] of entries) {
                flashHandlers[level](message);
            }
        })

        return () => {
            removeListener();
        }
    }, []);

    return (
        <div className="min-h-screen bg-background text-foreground">
            <div className="pointer-events-none absolute inset-x-0 top-0 -z-10 h-128 overflow-hidden">
                <div className="absolute top-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-primary/15 blur-3xl" />
                <div className="absolute top-20 right-0 h-56 w-56 rounded-full bg-primary/10 blur-3xl" />
            </div>

            <AppHeader auth={auth} />
            <main>{children}</main>
            <Toaster closeButton richColors position="top-center"/>
        </div>
    );
}
