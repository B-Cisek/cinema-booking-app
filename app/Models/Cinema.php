<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $city
 * @property string $street
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Table(timestamps: true)]
#[Fillable(['city', 'street'])]
class Cinema extends Model
{
    use HasFactory, HasUuids;

    public function halls(): HasMany
    {
        return $this->hasMany(Hall::class);
    }
}
