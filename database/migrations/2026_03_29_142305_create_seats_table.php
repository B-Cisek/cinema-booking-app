<?php

use App\Models\Hall;
use App\RowLabel;
use App\SeatType;
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
        Schema::create('seats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Hall::class, 'hall_id')->constrained()->cascadeOnDelete();
            $table->enum('row_label', RowLabel::cases());
            $table->integer('seat_number');
            $table->enum('seat_type', SeatType::cases());
            $table->integer('pos_x');
            $table->integer('pos_y');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['hall_id', 'row_label', 'seat_number']);
            $table->unique(['hall_id', 'pos_x', 'pos_y']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
