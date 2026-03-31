<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Screening;
use App\ScreeningStatus;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $selectedCinema = Cinema::query()->find(
            $request->session()->get(
                SelectCinemaController::CINEMA_KEY,
                $request->cookie(SelectCinemaController::CINEMA_KEY),
            ),
        );

        $dateRangeStart = CarbonImmutable::now()->startOfDay();
        $dateRangeEnd = $dateRangeStart->addDays(6)->endOfDay();

        $scheduleDays = collect(range(0, 6))
            ->map(function (int $offset) use ($dateRangeStart): array {
                $day = $dateRangeStart->addDays($offset)->locale('pl');

                return [
                    'date' => $day->toDateString(),
                    'label' => $day->translatedFormat('j F'),
                    'relative_label' => match ($offset) {
                        0 => 'Dzisiaj',
                        1 => 'Jutro',
                        default => ucfirst($day->translatedFormat('l')),
                    },
                ];
            })
            ->values();

        $screenings = collect();

        if ($selectedCinema !== null) {
            $screenings = Screening::query()
                ->with([
                    'hall:id,cinema_id,label',
                    'movie:id,title,description,duration,poster_url',
                ])
                ->where('status', ScreeningStatus::SCHEDULED)
                ->whereHas('hall', function ($query) use ($selectedCinema): void {
                    $query->where('cinema_id', $selectedCinema->getKey());
                })
                ->whereBetween('starts_at', [$dateRangeStart, $dateRangeEnd])
                ->orderBy('starts_at')
                ->get()
                ->map(function (Screening $screening): array {
                    return [
                        'id' => $screening->getKey(),
                        'date' => $screening->starts_at->toDateString(),
                        'starts_at' => $screening->starts_at->format('H:i'),
                        'ends_at' => $screening->ends_at->format('H:i'),
                        'status' => $screening->status->value,
                        'hall' => [
                            'label' => $screening->hall->label,
                        ],
                        'movie' => [
                            'title' => $screening->movie->title,
                            'description' => $screening->movie->description,
                            'duration' => $screening->movie->duration,
                            'poster_url' => $screening->movie->poster_url,
                        ],
                    ];
                })
                ->values();
        }

        return Inertia::render('home', [
            'cinemas' => Cinema::all(),
            'selectedCinema' => $selectedCinema?->only(['id', 'city', 'street']),
            'scheduleDays' => $scheduleDays,
            'screenings' => $screenings,
        ]);
    }
}
