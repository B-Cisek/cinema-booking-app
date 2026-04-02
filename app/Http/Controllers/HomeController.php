<?php

namespace App\Http\Controllers;

use App\Queries\GetHomeScreeningsQuery;
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
    ) {}

    public function __invoke(Request $request): Response|ResponseFactory
    {
        // TODO: fix later
        $selectedCinema = $request->session()->get(SelectCinemaController::CINEMA_SESSION_KEY);

        if (! $selectedCinema) {
            Inertia::flash('error', 'Please select a cinema');
        }

        return Inertia::render('Home', [
            'scheduleDays' => $this->scheduleDaysFactory->make(),
            'screenings' => $selectedCinema ? $this->screeningsQuery->execute($selectedCinema) : [],
        ]);
    }
}
