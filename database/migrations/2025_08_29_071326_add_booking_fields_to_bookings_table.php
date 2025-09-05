<?php

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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('service_type')->nullable()->after('hub_owner_id');
            $table->string('seat_id')->nullable()->after('service_type');
            $table->string('seat_label')->nullable()->after('seat_id');
            $table->time('booking_time')->nullable()->after('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'seat_id', 'seat_label', 'booking_time']);
        });
    }
};
