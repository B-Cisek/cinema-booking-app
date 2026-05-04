<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string|null $user_id
 * @property string|null $guest_id
 * @property string $screening_id
 * @property string $booking_number
 * @property BookingStatus $status
 * @property PaymentMethod $payment_method
 * @property string $customer_email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Screening $screening
 * @property User $user
 * @property Collection<BookedSeat> $bookedSeats
 */
#[Table(timestamps: true)]
#[Fillable(['user_id', 'guest_id', 'screening_id', 'booking_number', 'status', 'customer_email', 'payment_method'])]
class Booking extends Model
{
    use HasUuids;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function screening(): BelongsTo
    {
        return $this->belongsTo(Screening::class);
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
            'status' => BookingStatus::class,
            'payment_method' => PaymentMethod::class,
        ];
    }
}
