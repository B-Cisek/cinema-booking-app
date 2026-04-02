<?php

namespace App\Data;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class SeatMap
{
    /**
     * @var array<string, SeatView>
     */
    private array $seats;

    /**
     * @param  Collection<SeatView>  $seats
     */
    public function __construct(Collection $seats)
    {
        $sortedSeats = $seats
            ->sortBy(fn (SeatView $seat): string => sprintf(
                '%05d-%05d',
                $seat->posY,
                $seat->posX,
            ))
            ->values();

        $this->seats = $sortedSeats
            ->mapWithKeys(fn (SeatView $seat): array => [
                $this->normalizeLabel($seat->label()) => $seat,
            ])
            ->all();
    }

    public function seat(string $label): SeatView
    {
        $normalizedLabel = $this->normalizeLabel($label);

        if (! array_key_exists($normalizedLabel, $this->seats)) {
            throw new InvalidArgumentException("Seat [{$label}] does not exist in this seat map.");
        }

        return $this->seats[$normalizedLabel];
    }

    public function hasSeat(string $label): bool
    {
        return array_key_exists($this->normalizeLabel($label), $this->seats);
    }

    /**
     * @return array<int, SeatView>
     */
    public function all(): array
    {
        return array_values($this->seats);
    }

    /**
     * @return array<int, SeatView>
     */
    public function availableSeats(): array
    {
        return array_values(array_filter(
            $this->seats,
            fn (SeatView $seat): bool => $seat->isAvailable(),
        ));
    }

    /**
     * @return array<int, array{label: string, seats: array<int, SeatView>}>
     */
    public function rows(): array
    {
        return collect($this->all())
            ->groupBy(fn (SeatView $seat): string => $seat->row->value)
            ->map(function ($seats, string $rowLabel): array {
                return [
                    'label' => $rowLabel,
                    'seats' => $seats->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function normalizeLabel(string $label): string
    {
        return Str::of($label)
            ->upper()
            ->trim();
    }
}
