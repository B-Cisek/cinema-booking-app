<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use Illuminate\Database\Seeder;

class CinemaSeeder extends Seeder
{
    public function run(): void
    {
        $cinemas = [
            [
                'street' => 'ul. Złota 11',
                'city' => 'Warszawa',
            ],
            [
                'street' => 'ul. Adamowskiego 10',
                'city' => 'Rzeszów',
            ],
            [
                'street' => 'ul. Kopernika 55',
                'city' => 'Lublin',
            ],
            [
                'street' => 'ul. Sportowa 12/3',
                'city' => 'Kraków',
            ],
            [
                'street' => 'ul. Grunwaldzka 82',
                'city' => 'Gdańsk',
            ],
            [
                'street' => 'ul. Piotrkowska 67',
                'city' => 'Łódź',
            ],
        ];

        foreach ($cinemas as $cinemaData) {
            Cinema::query()->updateOrCreate($cinemaData, $cinemaData);
        }
    }
}
