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
        if (Schema::hasTable('disputes')) {
            return;
        }

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            // Keep IDs flexible to work with imported schemas where key types may differ.
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('hub_owner_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->enum('type', ['payment', 'service', 'behavior', 'other']);
            $table->text('description');
            $table->text('evidence')->nullable();
            $table->enum('status', ['open', 'resolved', 'escalated'])->default('open');
            $table->text('resolution')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
