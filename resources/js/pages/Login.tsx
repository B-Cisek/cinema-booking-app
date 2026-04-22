import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowRight, LockKeyhole, Mail } from 'lucide-react';
import type { FormEvent } from 'react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home, register } from '@/routes';
import loginRoutes from '@/routes/login';

interface LoginForm {
    email: string;
    password: string;
    remember: boolean;
}

export default function Login() {
    const form = useForm<LoginForm>({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (event: FormEvent<HTMLFormElement>): void => {
        event.preventDefault();
        form.post(loginRoutes.store.url(), {
            onFinish: () => {
                form.reset('password');
            },
        });
    };

    return (
        <>
            <Head title="Logowanie" />

            <section className="mx-auto flex min-h-[calc(100vh-5.5rem)] w-full max-w-6xl items-center px-4 py-10 sm:px-6">
                <div className="grid w-full gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                    <div className="space-y-6">
                        <div className="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
                            <LockKeyhole className="size-4" />
                            Strefa klienta Cinema
                        </div>

                        <div className="space-y-4">
                            <h1 className="max-w-xl text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                                Zaloguj się i szybciej finalizuj rezerwacje.
                            </h1>
                            <p className="max-w-xl text-base leading-7 text-muted-foreground sm:text-lg">
                                Zachowaj wygodny dostęp do swojego konta i wróć
                                do repertuaru bez zbędnych kroków.
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
                                href={register()}
                                className="inline-flex items-center gap-2 font-medium text-primary transition hover:opacity-80"
                            >
                                Nie masz konta? Załóż je teraz
                                <ArrowRight className="size-4" />
                            </Link>
                        </div>
                    </div>

                    <Card className="rounded-[2rem] border-border/70 shadow-2xl shadow-primary/10">
                        <CardHeader className="space-y-2 px-6 pt-6 sm:px-8 sm:pt-8">
                            <CardTitle className="text-2xl">
                                Logowanie
                            </CardTitle>
                            <CardDescription>
                                Użyj adresu e-mail i hasła przypisanego do
                                konta.
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
                                            placeholder="Wpisz hasło"
                                            autoComplete="current-password"
                                        />
                                    </div>
                                    {form.errors.password ? (
                                        <p className="text-sm text-destructive">
                                            {form.errors.password}
                                        </p>
                                    ) : null}
                                </div>

                                <label className="flex items-center gap-3 text-sm text-muted-foreground">
                                    <input
                                        type="checkbox"
                                        checked={form.data.remember}
                                        onChange={(event) =>
                                            form.setData(
                                                'remember',
                                                event.target.checked,
                                            )
                                        }
                                        className="size-4 rounded border-border text-primary"
                                    />
                                    Zapamiętaj mnie na tym urządzeniu
                                </label>

                                <Button
                                    type="submit"
                                    size="lg"
                                    className="w-full rounded-2xl shadow-lg shadow-primary/20"
                                    disabled={form.processing}
                                >
                                    {form.processing
                                        ? 'Logowanie...'
                                        : 'Zaloguj się'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </section>
        </>
    );
}
