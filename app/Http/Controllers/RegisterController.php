<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\RegisterUser;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(private readonly RegisterUser $registerUser) {}

    public function show(): Response
    {
        return Inertia::render('Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $this->registerUser->handle(
            email: $request->validated('email'),
            password: $request->validated('password')
        );

        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
