<?php

declare(strict_types=1);

namespace App\Commands;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

readonly class RegisterUser
{
    public function handle(string $email, #[\SensitiveParameter] string $password): void
    {
        $user = User::query()->create([
            'email' => $email,
            'password' => $password,
        ]);

        Auth::login($user);

        UserRegistered::dispatch($user);
    }
}
