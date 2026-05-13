import { Link } from '@inertiajs/react';
import { LogOut, Ticket, UserRound } from 'lucide-react';
import CinemaPicker from '@/components/CinemaPicker';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { home, login, logout, register } from '@/routes';
import type { Auth } from '@/types';
import HeaderUser from '@/components/HeaderUser';

interface AppHeaderProps {
    auth: Auth;
}

export default function AppHeader({ auth }: AppHeaderProps) {
    return (
        <header className="sticky top-0 z-40 border-b border-border/70 bg-background/85 backdrop-blur-xl">
            <div className="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                <Link
                    href={home()}
                    className="flex items-center gap-3 rounded-2xl transition hover:opacity-90"
                >
                    <div className="flex size-11 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-lg shadow-primary/20">
                        <Ticket className="size-5" />
                    </div>

                    <p className="text-xl font-bold tracking-wider text-primary uppercase">
                        Cinema
                    </p>
                </Link>

                <div className="flex items-center gap-2 sm:gap-3">
                    <CinemaPicker />

                    {auth.user ? (
                        <HeaderUser user={auth.user} />
                    ) : (
                        <>
                            <Button
                                asChild
                                variant="ghost"
                                className="hidden sm:inline-flex"
                            >
                                <Link href={login()}>Zaloguj się</Link>
                            </Button>
                            <Button
                                asChild
                                className="shadow-lg shadow-primary/20"
                            >
                                <Link href={register()}>Rejestracja</Link>
                            </Button>
                        </>
                    )}
                </div>
            </div>
        </header>
    );
}
