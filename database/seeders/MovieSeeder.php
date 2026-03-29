<?php

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
        ];

        foreach ($movies as $movieData) {
            Movie::create($movieData);
        }
    }
}
