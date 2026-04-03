<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cinema;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cinemas = Cinema::all();

        $halls = [
            [
                'name' => 'hall_1',
                'label' => 'Sala 1',
            ],
            [
                'name' => 'hall_2',
                'label' => 'Sala 2',
            ],
            [
                'name' => 'hall_3',
                'label' => 'Sala 3',
            ],
        ];

        foreach ($halls as $hall) {
            $cinemas->each(fn (Cinema $cinema) => $cinema->halls()->create($hall));
        }
    }
}
