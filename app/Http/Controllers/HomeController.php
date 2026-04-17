<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Identity\CinemaResolver;
use App\Support\Translation\UseTranslations;
use App\ViewData\HomePageData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function __construct(
        private readonly CinemaResolver $cinemaResolver,
        private readonly HomePageData $data
    ) {}

    #[UseTranslations(key: 'home')]
    public function __invoke(Request $request): Response|ResponseFactory
    {
        $cinema = $this->cinemaResolver->resolve($request);

        if ($cinema === null) {
            Inertia::flash('error', __('home.select_cinema_message'));
        }

        return Inertia::render('Home', $this->data->build($cinema));
    }
}
