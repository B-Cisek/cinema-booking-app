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
import { Checkbox } from '@/components/ui/checkbox';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
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
                        <div className="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
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
                            <Link
                                href={register()}
                                className="inline-flex items-center gap-2 font-medium text-primary transition hover:opacity-80"
                            >
                                Nie masz konta? Załóż je teraz
                                <ArrowRight className="size-4" />
                            </Link>
                        </div>
                    </div>

                    <Card className="rounded-xl border-border/70 shadow-2xl shadow-primary/10">
                        <CardHeader className="space-y-1 px-6 pt-4 sm:px-8">
                            <CardTitle className="text-2xl">
                                Logowanie
                            </CardTitle>
                            <CardDescription>
                                Użyj adresu e-mail i hasła przypisanego do
                                konta.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-6 pb-3 sm:px-8">
                            <form onSubmit={handleSubmit} className="space-y-5">
                                <Field className="gap-2">
                                    <FieldLabel htmlFor="email">
                                        E-mail
                                    </FieldLabel>
                                    <div className="relative">
                                        <Mail className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="email"
                                            type="email"
                                            value={form.data.email}
                                            onChange={(event) =>
                                                form.setData(
                                                    'email',
                                                    event.target.value,
                                                )
                                            }
                                            className="h-12 rounded-xl bg-background pl-10 text-sm md:text-sm"
                                            placeholder="twoj@email.pl"
                                            autoComplete="email"
                                        />
                                    </div>
                                    {form.errors.email ? (
                                        <FieldError>
                                            {form.errors.email}
                                        </FieldError>
                                    ) : null}
                                </Field>

                                <Field className="gap-2">
                                    <FieldLabel htmlFor="password">
                                        Hasło
                                    </FieldLabel>
                                    <div className="relative">
                                        <LockKeyhole className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                                        <Input
                                            id="password"
                                            type="password"
                                            value={form.data.password}
                                            onChange={(event) =>
                                                form.setData(
                                                    'password',
                                                    event.target.value,
                                                )
                                            }
                                            className="h-12 rounded-xl bg-background pl-10 text-sm md:text-sm"
                                            placeholder="Wpisz hasło"
                                            autoComplete="current-password"
                                        />
                                    </div>
                                    {form.errors.password ? (
                                        <FieldError>
                                            {form.errors.password}
                                        </FieldError>
                                    ) : null}
                                </Field>

                                <Label
                                    htmlFor="remember"
                                    className="flex items-center gap-3 text-sm text-muted-foreground"
                                >
                                    <Checkbox
                                        id="remember"
                                        checked={form.data.remember}
                                        onCheckedChange={(checked) =>
                                            form.setData(
                                                'remember',
                                                checked === true,
                                            )
                                        }
                                    />
                                    Zapamiętaj mnie na tym urządzeniu
                                </Label>

                                <Button
                                    type="submit"
                                    size="lg"
                                    className="w-full cursor-pointer rounded-xl py-4 shadow-lg shadow-primary/20"
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
