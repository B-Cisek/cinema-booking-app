<?php

declare(strict_types=1);

namespace App\Commands;

use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

readonly class LoginUser
{
    public function handle(string $email, #[\SensitiveParameter] string $password, bool $remember): void
    {
        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        UserLoggedIn::dispatch(Auth::user());
    }
}
