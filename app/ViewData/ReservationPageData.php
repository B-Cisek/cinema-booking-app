<?php

declare(strict_types=1);

namespace App\ViewData;

use App\Models\Screening;
use App\Support\Hall\CinemaHallFactory;
use Illuminate\Support\Facades\App;

readonly class ReservationPageData
{
    public function __construct(private CinemaHallFactory $cinemaHallFactory) {}

    public function build(Screening $screening): array
    {
        $layout = $this->cinemaHallFactory->forScreening($screening->id);

        return [
            'seats' => $layout->rows(),
            'screening' => [
                'id' => $screening->getKey(),
                'starts_at' => $screening->starts_at->format('H:i'),
                'ends_at' => $screening->ends_at->format('H:i'),
                'date' => $screening->starts_at->locale(App::currentLocale())->translatedFormat('j F Y'),
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
        ];
    }
}
