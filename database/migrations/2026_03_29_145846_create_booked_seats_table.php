<?php

declare(strict_types=1);

use App\Models\Booking;
use App\Models\Seat;
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
        Schema::create('booked_seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Booking::class, 'booking_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Seat::class, 'seat_id')->constrained()->cascadeOnDelete();
            $table->integer('price');
            $table->timestamps();

            $table->unique(['booking_id', 'seat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booked_seats');
    }
};
