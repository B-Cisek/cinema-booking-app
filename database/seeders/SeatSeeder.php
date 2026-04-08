<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RowLabel;
use App\Enums\SeatType;
use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $halls = Hall::query()->get();
        $seatsPerRow = Config::integer('app.hall.max_seats_per_row');

        foreach ($halls as $hall) {
            $desiredSeats = collect();

            foreach (RowLabel::cases() as $rowIndex => $rowLabel) {
                foreach ($this->positionsForHallRow($hall->name, $rowIndex, $seatsPerRow) as $position) {
                    $desiredSeats->push([
                        'hall_id' => $hall->getKey(),
                        'row_label' => $rowLabel,
                        'seat_number' => $position,
                        'seat_type' => SeatType::STANDARD,
                        'pos_x' => $position,
                        'pos_y' => $rowIndex + 1,
                        'is_active' => ! $this->shouldDisableSeat($position, $rowIndex),
                    ]);
                }
            }

            $this->syncHallSeats($hall->getKey(), $desiredSeats);
        }
    }

    /**
     * @return array<int, int>
     */
    private function positionsForHallRow(string $hallName, int $rowIndex, int $seatsPerRow): array
    {
        return match ($hallName) {
            'hall_1' => range(1, $seatsPerRow),
            'hall_2' => $this->positionsForHallTwo($rowIndex, $seatsPerRow),
            'hall_3' => $this->positionsForHallThree($rowIndex, $seatsPerRow),
            'hall_4' => $this->positionsForHallFour($rowIndex, $seatsPerRow),
            default => range(1, $seatsPerRow),
        };
    }

    /**
     * @return array<int, int>
     */
    private function positionsForHallTwo(int $rowIndex, int $seatsPerRow): array
    {
        $positions = range(1, $seatsPerRow);

        if ($rowIndex <= 7) {
            return array_values(array_diff($positions, [13]));
        }

        return array_values(array_diff($positions, [12, 13, 14]));
    }

    /**
     * @return array<int, int>
     */
    private function positionsForHallThree(int $rowIndex, int $seatsPerRow): array
    {
        $positions = range(1, $seatsPerRow);

        if ($rowIndex <= 2) {
            return array_values(array_diff($positions, [1, 2, 24, 25]));
        }

        if ($rowIndex <= 5) {
            return array_values(array_diff($positions, [1, 25]));
        }

        return $positions;
    }

    /**
     * @return array<int, int>
     */
    private function positionsForHallFour(int $rowIndex, int $seatsPerRow): array
    {
        $positions = range(1, $seatsPerRow);

        if ($rowIndex <= 3) {
            return array_values(array_diff($positions, range(1, 4), range(22, $seatsPerRow)));
        }

        return array_values(array_diff($positions, range(1, 3), range(23, $seatsPerRow), [13]));
    }

    /**
     * @param  Collection<int, array{
     *     hall_id: string,
     *     row_label: RowLabel,
     *     seat_number: int,
     *     seat_type: SeatType,
     *     pos_x: int,
     *     pos_y: int,
     *     is_active: bool
     * }>  $desiredSeats
     */
    private function syncHallSeats(string $hallId, Collection $desiredSeats): void
    {
        $desiredKeys = [];

        foreach ($desiredSeats as $seatData) {
            Seat::query()->updateOrCreate(
                [
                    'hall_id' => $seatData['hall_id'],
                    'row_label' => $seatData['row_label'],
                    'seat_number' => $seatData['seat_number'],
                ],
                [
                    'seat_type' => $seatData['seat_type'],
                    'pos_x' => $seatData['pos_x'],
                    'pos_y' => $seatData['pos_y'],
                    'is_active' => $seatData['is_active'],
                ],
            );

            $desiredKeys[] = $this->seatKey(
                $seatData['row_label'],
                $seatData['seat_number'],
            );
        }

        Seat::query()
            ->where('hall_id', $hallId)
            ->get()
            ->reject(fn (Seat $seat): bool => in_array(
                $this->seatKey($seat->row_label, $seat->seat_number),
                $desiredKeys,
                true,
            ))
            ->each
            ->delete();
    }

    private function shouldDisableSeat(int $seatNumber, int $rowIndex): bool
    {
        return $rowIndex === 0 && in_array($seatNumber, [1, 2, 24, 25], true);
    }

    private function seatKey(RowLabel $rowLabel, int $seatNumber): string
    {
        return sprintf('%s-%d', $rowLabel->value, $seatNumber);
    }
}
