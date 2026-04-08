<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Projekt Hail Mary',
                'description' => 'Astronauta próbuje uratować Ziemię, będąc sam w przestrzeni kosmicznej.',
                'duration' => 156,
                'poster_url' => 'https://fwcdn.pl/fpo/78/41/10047841/8232509.8.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Peaky Blinders: Nieśmiertelny',
                'description' => 'Gdy jego syn zostaje wplątany w nazistowski spisek, Tommy Shelby opuszcza miejsce wygnania i wraca do Birmingham, aby ratować rodzinę i kraj.',
                'duration' => 114,
                'poster_url' => 'https://fwcdn.pl/fpo/02/41/10050241/8228756.8.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Drama',
                'description' => 'Pozornie idealna para przechodzi kryzys, gdy na kilka dni przed ślubem na jaw wychodzą skrywane tajemnice.',
                'duration' => 105,
                'poster_url' => 'https://fwcdn.pl/fpo/34/74/10063474/8231662.8.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Zwierzogród 2',
                'description' => 'Policjanci Judy Hops i Nick Wilde ponownie łączą siły, aby rozwiązać nową sprawę. Trop prowadzi ich do poszukiwanego kryminalisty, węża imieniem Gary.',
                'duration' => 107,
                'poster_url' => 'https://fwcdn.pl/fpo/14/62/10051462/8223725.10.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Jedna bitwa po drugiej',
                'description' => 'Gdy po latach powraca ich śmiertelny wróg, grupa byłych rewolucjonistów jednoczy się, aby uratować córkę swego towarzysza.',
                'duration' => 161,
                'poster_url' => 'https://fwcdn.pl/fpo/98/94/10059894/8198621.10.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Wielki Marty',
                'description' => 'Marty, w którego marzenia nikt nie wierzy, nie cofnie się przed niczym, by rzucić świat na kolana.',
                'duration' => 149,
                'poster_url' => 'https://fwcdn.pl/fpo/69/99/10056999/8214535.10.webp',
                'is_active' => true,
            ],
            [
                'title' => 'Hopnięci',
                'description' => '19-letnia miłośniczka zwierząt wykorzystuje technologię, która pozwala przenieść jej świadomość do ciała robotycznego bobra, by odkrywać tajemnice świata natury.',
                'duration' => 105,
                'poster_url' => 'https://fwcdn.pl/fpo/85/75/10058575/8223400.10.webp',
                'is_active' => true,
            ],
        ];

        foreach ($movies as $movieData) {
            Movie::query()->updateOrCreate(
                ['title' => $movieData['title']],
                $movieData,
            );
        }
    }
}
