<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use App\Services\ScreeningSeatMap;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationController extends Controller
{
    public function __construct(private readonly ScreeningSeatMap $screeningSeatMap)
    {
    }

    public function __invoke(Screening $screening): Response
    {
        $map = $this->screeningSeatMap->for($screening->getKey());

        return Inertia::render('Reservation', [
            'seats' => $map->rows(),
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $screening->starts_at->format('H:i'),
                'ends_at' => $screening->ends_at->format('H:i'),
                'date' => $screening->starts_at->locale('pl')->translatedFormat('j F Y'),
                'hall' => [
                    'label' => $screening->hall->label,
                    'cinema' => [
                        'city' => $screening->hall->cinema->city,
                        'street' => $screening->hall->cinema->street,
                    ],
                ],
                'movie' => [
                    'title' => $screening->movie->title,
                    'description' => $screening->movie->description,
                    'duration' => $screening->movie->duration,
                    'poster_url' => $screening->movie->poster_url,
                ],
            ],
        ]);
    }
}
