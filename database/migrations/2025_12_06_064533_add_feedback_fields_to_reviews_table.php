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
        Schema::table('reviews', function (Blueprint $table) {
            // Only add booking_id if it doesn't exist
            if (!Schema::hasColumn('reviews', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('hub_owner_id')->constrained('bookings')->onDelete('set null');
            }
            
            // Add feedback_type if it doesn't exist
            if (!Schema::hasColumn('reviews', 'feedback_type')) {
                $table->enum('feedback_type', ['workspace', 'platform'])->default('workspace')->after('comment');
            }
            
            // Add priority if it doesn't exist
            if (!Schema::hasColumn('reviews', 'priority')) {
                $table->integer('priority')->default(0)->after('status'); // 0 = normal, 1 = high priority
            }
            
            // Add is_flagged if it doesn't exist
            if (!Schema::hasColumn('reviews', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('priority'); // Auto-flagged for suspicious content
            }
            
            // Add moderation_notes if it doesn't exist
            if (!Schema::hasColumn('reviews', 'moderation_notes')) {
                $table->text('moderation_notes')->nullable()->after('rejected_by'); // Admin notes
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['booking_id', 'feedback_type', 'priority', 'is_flagged', 'moderation_notes']);
        });
    }
};
