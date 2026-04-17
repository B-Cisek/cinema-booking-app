<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Commands\SelectCinema;
use App\Http\Requests\SelectCinemaRequest;
use Illuminate\Http\RedirectResponse;

class SelectCinemaController extends Controller
{
    public function __construct(private readonly SelectCinema $selectCinema) {}

    public function __invoke(SelectCinemaRequest $request): RedirectResponse
    {
        $this->selectCinema->handle($request->validated('id'));

        return redirect()->route('home');
    }
}
