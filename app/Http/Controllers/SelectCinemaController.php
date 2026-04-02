<?php

namespace App\Http\Controllers;

use App\Actions\SelectCinema;
use App\Http\Requests\SelectCinemaRequest;
use Illuminate\Http\RedirectResponse;

class SelectCinemaController extends Controller
{
    public function __construct(private readonly SelectCinema $selectCinema) {}

    public function __invoke(SelectCinemaRequest $request): RedirectResponse
    {
        $this->selectCinema->handle($request, $request->cinemaId());

        return redirect()->route('home');
    }
}
