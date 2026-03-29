<?php

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
            'Rzeszów',
            'Warszawa',
            'Kraków',
            'Lublin',
        ];

        foreach ($cities as $city) {
            Cinema::create([
                'city' => $city,
            ]);
        }
    }
}
