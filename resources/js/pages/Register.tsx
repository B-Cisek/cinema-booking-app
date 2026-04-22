import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowRight, BadgeCheck, LockKeyhole, Mail } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home, login } from '@/routes';
import registerRoutes from '@/routes/register';

interface RegisterForm {
    email: string;
    password: string;
    password_confirmation: string;
}

export default function Register() {
    const form = useForm<RegisterForm>({
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>): void => {
        event.preventDefault();
        form.post(registerRoutes.store.url(), {
            onFinish: () => {
                form.reset('password', 'password_confirmation');
            },
        });
    };

    return (
        <>
            <Head title="Rejestracja" />

            <section className="mx-auto flex min-h-[calc(100vh-5.5rem)] w-full max-w-6xl items-center px-4 py-10 sm:px-6">
                <div className="grid w-full gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                    <Card className="order-2 rounded-[2rem] border-border/70 shadow-2xl shadow-primary/10 lg:order-1">
                        <CardHeader className="space-y-2 px-6 pt-6 sm:px-8 sm:pt-8">
                            <CardTitle className="text-2xl">
                                Załóż konto
                            </CardTitle>
                            <CardDescription>
                                Rejestracja zajmie chwilę i od razu zaloguje Cię
                                do aplikacji.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-6 pb-6 sm:px-8 sm:pb-8">
                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div className="space-y-2">
                                    <label
                                        htmlFor="email"
                                        className="text-sm font-medium"
                                    >
                                        E-mail
                                    </label>
                                    <div className="flex items-center gap-3 rounded-2xl border border-border bg-background px-4 py-3 transition focus-within:border-primary/50 focus-within:ring-3 focus-within:ring-primary/10">
                                        <Mail className="size-4 text-muted-foreground" />
                                        <input
                                            id="email"
                                            type="email"
                                            value={form.data.email}
                                            onChange={(event) =>
                                                form.setData(
                                                    'email',
                                                    event.target.value,
                                                )
                                            }
                                            className="w-full bg-transparent text-sm outline-none"
                                            placeholder="twoj@email.pl"
                                            autoComplete="email"
                                        />
                                    </div>
                                    {form.errors.email ? (
                                        <p className="text-sm text-destructive">
                                            {form.errors.email}
                                        </p>
                                    ) : null}
                                </div>

                                <div className="space-y-2">
                                    <label
                                        htmlFor="password"
                                        className="text-sm font-medium"
                                    >
                                        Hasło
                                    </label>
                                    <div className="flex items-center gap-3 rounded-2xl border border-border bg-background px-4 py-3 transition focus-within:border-primary/50 focus-within:ring-3 focus-within:ring-primary/10">
                                        <LockKeyhole className="size-4 text-muted-foreground" />
                                        <input
                                            id="password"
                                            type="password"
                                            value={form.data.password}
                                            onChange={(event) =>
                                                form.setData(
                                                    'password',
                                                    event.target.value,
                                                )
                                            }
                                            className="w-full bg-transparent text-sm outline-none"
                                            placeholder="Minimum 8 znaków"
                                            autoComplete="new-password"
                                        />
                                    </div>
                                    {form.errors.password ? (
                                        <p className="text-sm text-destructive">
                                            {form.errors.password}
                                        </p>
                                    ) : null}
                                </div>

                                <div className="space-y-2">
                                    <label
                                        htmlFor="password_confirmation"
                                        className="text-sm font-medium"
                                    >
                                        Powtórz hasło
                                    </label>
                                    <div className="flex items-center gap-3 rounded-2xl border border-border bg-background px-4 py-3 transition focus-within:border-primary/50 focus-within:ring-3 focus-within:ring-primary/10">
                                        <BadgeCheck className="size-4 text-muted-foreground" />
                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            value={
                                                form.data.password_confirmation
                                            }
                                            onChange={(event) =>
                                                form.setData(
                                                    'password_confirmation',
                                                    event.target.value,
                                                )
                                            }
                                            className="w-full bg-transparent text-sm outline-none"
                                            placeholder="Powtórz hasło"
                                            autoComplete="new-password"
                                        />
                                    </div>
                                </div>

                                <Button
                                    type="submit"
                                    size="lg"
                                    className="w-full rounded-2xl shadow-lg shadow-primary/20"
                                    disabled={form.processing}
                                >
                                    {form.processing
                                        ? 'Tworzenie konta...'
                                        : 'Utwórz konto'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>

                    <div className="order-1 space-y-6 lg:order-2 lg:pl-8">
                        <div className="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
                            <BadgeCheck className="size-4" />
                            Nowe konto w Cinema
                        </div>

                        <div className="space-y-4">
                            <h1 className="max-w-xl text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                                Rejestracja otwiera prostszy powrót do seansów.
                            </h1>
                            <p className="max-w-xl text-base leading-7 text-muted-foreground sm:text-lg">
                                Załóż konto, aby wygodnie zarządzać dostępem do
                                aplikacji i przechodzić do rezerwacji bez
                                dodatkowych kroków.
                            </p>
                        </div>

                        <div className="flex flex-wrap items-center gap-3 text-sm">
                            <Button
                                asChild
                                variant="outline"
                                className="rounded-2xl"
                            >
                                <Link href={home()}>Wróć do repertuaru</Link>
                            </Button>
                            <Link
                                href={login()}
                                className="inline-flex items-center gap-2 font-medium text-primary transition hover:opacity-80"
                            >
                                Masz już konto? Zaloguj się
                                <ArrowRight className="size-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}
