<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = Hall::all();

        $seatsPerRow = 14;

        foreach ($halls as $hall) {
            foreach (RowLabel::cases() as $rowIndex => $rowLabel) {
                for ($seatNumber = 1; $seatNumber <= $seatsPerRow; $seatNumber++) {
                    Seat::updateOrCreate(
                        [
                            'hall_id' => $hall->id,
                            'row_label' => $rowLabel,
                            'seat_number' => $seatNumber,
                        ],
                        [
                            'seat_type' => SeatType::STANDARD,
                            'pos_x' => $seatNumber,
                            'pos_y' => $rowIndex + 1,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
