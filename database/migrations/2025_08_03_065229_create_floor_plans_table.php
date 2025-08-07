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
        Schema::create('floor_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_owner_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->default('My Floor Plan');
            $table->json('layout_data'); // Stores the floor plan items and positions
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor_plans');
    }
};
