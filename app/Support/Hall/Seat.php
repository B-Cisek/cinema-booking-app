<?php

declare(strict_types=1);

namespace App\Support\Hall;

use App\Enums\RowLabel;
use App\Enums\SeatType;

final readonly class Seat
{
    public function __construct(
        public string $id,
        public RowLabel $row,
        public int $seatNumber,
        public SeatType $seatType,
        public int $posX,
        public int $posY,
        public bool $isActive,
        public bool $isBooked,
    ) {}

    public function label(): string
    {
        return $this->row->value.$this->seatNumber;
    }

    public function isAvailable(): bool
    {
        return $this->isActive && ! $this->isBooked;
    }
}
