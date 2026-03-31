<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCinemaException;
use App\Repositories\CinemaRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SelectCinemaController extends Controller
{
    public const string CINEMA_KEY = 'cinema_id';

    public function __construct(private readonly CinemaRepository $cinemaRepository) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $id = $request->input('id');

        if ($id) {
            throw_if(
                ! $this->cinemaRepository->isExist($id),
                InvalidCinemaException::class,
            );

            $request->session()->put(self::CINEMA_KEY, $id);
        }

        return redirect()->route('home');
    }
}
