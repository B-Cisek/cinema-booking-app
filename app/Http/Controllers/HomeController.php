<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\GetHomeScreeningsQuery;
use App\Services\CinemaResolver;
use App\Services\ScheduleDaysFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class HomeController extends Controller
{
    public function __construct(
        private readonly ScheduleDaysFactory $scheduleDaysFactory,
        private readonly GetHomeScreeningsQuery $screeningsQuery,
        private readonly CinemaResolver $selectedCinemaResolver,
    ) {}

    public function __invoke(Request $request): Response|ResponseFactory
    {
        $selectedCinema = $this->selectedCinemaResolver->resolve($request);

        if ($selectedCinema === null) {
            Inertia::flash('error', __('home.select_cinema_message'));
        }

        return Inertia::render('Home', [
            'scheduleDays' => $this->scheduleDaysFactory->make(),
            'screenings' => $selectedCinema ? $this->screeningsQuery->execute($selectedCinema->getKey()) : [],
        ]);
    }
}
