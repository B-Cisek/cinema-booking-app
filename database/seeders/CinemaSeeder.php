<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use Illuminate\Database\Seeder;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
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
        ];

        foreach ($cities as $cityData) {
            Cinema::create($cityData);
        }
    }
}
