<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Manual occupancy blocks: date ranges an admin marks as unavailable
     * for a property because of a booking made outside the website (phone,
     * Airbnb, Booking.com, walk-in, etc.). Kept separate from `bookings`
     * because these never go through the inquiry/approve workflow.
     */
    public function up(): void
    {
        Schema::create('property_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_blocks');
    }
};
