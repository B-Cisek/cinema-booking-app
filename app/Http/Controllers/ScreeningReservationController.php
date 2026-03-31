<?php

namespace App\Http\Controllers;

use App\Models\Screening;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningReservationController extends Controller
{
    public function __invoke(Screening $screening): Response
    {
        $screening->loadMissing([
            'hall:id,cinema_id,label',
            'hall.cinema:id,city,street',
            'movie:id,title,description,duration,poster_url',
        ]);

        return Inertia::render('screenings/reservation', [
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
