import { Link } from '@inertiajs/react';
import { LogOut, Ticket, UserRound } from 'lucide-react';
import CinemaPicker from '@/components/CinemaPicker';
import { Button } from '@/components/ui/button';
import { home, login, logout, register } from '@/routes';
import type { Auth } from '@/types';

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
                        <div className="flex items-center gap-2">
                            <div className="hidden items-center gap-3 rounded-2xl border border-border bg-card px-4 py-2 sm:flex">
                                <div className="flex size-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                    <UserRound className="size-4" />
                                </div>
                                <div className="min-w-0">
                                    <p className="text-[0.68rem] font-semibold tracking-[0.22em] text-muted-foreground uppercase">
                                        Konto
                                    </p>
                                    <p className="max-w-56 truncate text-sm font-medium">
                                        {auth.user.email}
                                    </p>
                                </div>
                            </div>

                            <Button
                                asChild
                                variant="outline"
                                className="rounded-2xl"
                            >
                                <Link href={logout()} method="post" as="button">
                                    <LogOut className="size-4" />
                                    Wyloguj
                                </Link>
                            </Button>
                        </div>
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
