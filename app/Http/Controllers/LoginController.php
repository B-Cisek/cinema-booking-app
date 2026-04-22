<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\LoginUser;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(private readonly LoginUser $loginUser) {}

    public function show(): Response
    {
        return Inertia::render('Login');
    }

    /**
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->loginUser->handle(
            email: $request->validated('email'),
            password: $request->validated('password'),
            remember: $request->boolean('remember')
        );

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
