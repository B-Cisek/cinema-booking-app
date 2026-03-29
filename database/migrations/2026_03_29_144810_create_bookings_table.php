<?php

use App\BookingStatus;
use App\Models\Screening;
use App\Models\User;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Screening::class, 'screening_id')->constrained()->cascadeOnDelete();
            $table->string('customer_email');
            $table->string('booking_number')->unique();
            $table->enum('status', BookingStatus::cases());
            $table->timestamps();

            $table->index(['screening_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
