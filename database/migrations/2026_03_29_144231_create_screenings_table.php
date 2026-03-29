<?php

use App\Models\Hall;
use App\Models\Movie;
use App\ScreeningStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('screenings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Movie::class, 'movie_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Hall::class, 'hall_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ScreeningStatus::cases());
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();

            $table->index(['hall_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
