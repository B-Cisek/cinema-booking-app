<?php

namespace App\Models;

use App\RowLabel;
use App\SeatType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table(timestamps: true)]
#[Fillable(['hall_id', 'row_label', 'seat_number', 'seat_type', 'pos_x', 'pos_y', 'is_active'])]
class Seat extends Model
{
    use HasUuids;

    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    public function bookedSeats(): HasMany
    {
        return $this->hasMany(BookedSeat::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'row_label' => RowLabel::class,
            'seat_type' => SeatType::class,
            'is_active' => 'boolean',
        ];
    }
}
