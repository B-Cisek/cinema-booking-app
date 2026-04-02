<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
