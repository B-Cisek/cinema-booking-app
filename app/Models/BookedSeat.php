<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $booking_id
 * @property string $screening_id
 * @property string $seat_id
 * @property int $price
 * @property Booking $booking
 * @property Seat $seat
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Table(name: 'booked_seats', timestamps: true)]
#[Fillable(['booking_id', 'screening_id', 'seat_id', 'price'])]
class BookedSeat extends Model
{
    use HasUuids;

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}
