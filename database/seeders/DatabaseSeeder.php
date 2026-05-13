<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Telescope\Telescope;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Telescope::stopRecording();

        try {
            $this->call([
                CinemaSeeder::class,
                HallSeeder::class,
                SeatSeeder::class,
                MovieSeeder::class,
                ScreeningSeeder::class,
            ]);
        } finally {
            Telescope::startRecording();
        }
    }
}
