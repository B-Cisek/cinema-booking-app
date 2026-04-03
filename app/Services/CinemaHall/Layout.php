<?php

declare(strict_types=1);

namespace App\Services\CinemaHall;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class Layout
{
    /**
     * @var array<string, Seat>
     */
    private array $seats;

    /**
     * @param  Collection<int, Seat>  $seats
     */
    public function __construct(Collection $seats)
    {
        $sortedSeats = $seats
            ->sortBy(fn (Seat $seat): string => sprintf(
                '%05d-%05d',
                $seat->posY,
                $seat->posX,
            ))
            ->values();

        $this->seats = $sortedSeats
            ->mapWithKeys(fn (Seat $seat): array => [
                $this->normalizeLabel($seat->label()) => $seat,
            ])
            ->all();
    }

    public function seat(string $label): Seat
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
     * @return array<int, Seat>
     */
    public function all(): array
    {
        return array_values($this->seats);
    }

    /**
     * @return array<int, Seat>
     */
    public function availableSeats(): array
    {
        return array_values(array_filter(
            $this->seats,
            fn (Seat $seat): bool => $seat->isAvailable(),
        ));
    }

    /**
     * @return array<int, array{label: string, seats: array<int, Seat|null>}>
     */
    public function rows(): array
    {
        return collect($this->all())
            ->groupBy(fn (Seat $seat): string => $seat->row->value)
            ->map(function ($seats, string $rowLabel): array {
                return [
                    'label' => $rowLabel,
                    'seats' => $this->toFixedWidthRow($seats),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  iterable<Seat>  $seats
     * @return array<int, Seat|null>
     */
    private function toFixedWidthRow(iterable $seats): array
    {
        $seatsByPosition = collect($seats)->keyBy(
            fn (Seat $seat): int => $seat->posX,
        );

        return collect(range(1, Config::integer('app.hall.max_seats_per_row')))
            ->map(
                fn (int $position): ?Seat => $seatsByPosition->get($position),
            )
            ->all();
    }

    private function normalizeLabel(string $label): string
    {
        return Str::of($label)
            ->upper()
            ->trim()
            ->toString();
    }
}
