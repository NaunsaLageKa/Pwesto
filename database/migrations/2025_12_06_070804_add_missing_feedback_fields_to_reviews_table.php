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
            // Check and add feedback_type if it doesn't exist
            if (!Schema::hasColumn('reviews', 'feedback_type')) {
                $table->enum('feedback_type', ['workspace', 'platform'])->default('workspace')->after('comment');
            }
            
            // Check and add priority if it doesn't exist
            if (!Schema::hasColumn('reviews', 'priority')) {
                $table->integer('priority')->default(0)->after('status');
            }
            
            // Check and add is_flagged if it doesn't exist
            if (!Schema::hasColumn('reviews', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('priority');
            }
            
            // Check and add moderation_notes if it doesn't exist
            if (!Schema::hasColumn('reviews', 'moderation_notes')) {
                $table->text('moderation_notes')->nullable()->after('rejected_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'feedback_type')) {
                $table->dropColumn('feedback_type');
            }
            if (Schema::hasColumn('reviews', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('reviews', 'is_flagged')) {
                $table->dropColumn('is_flagged');
            }
            if (Schema::hasColumn('reviews', 'moderation_notes')) {
                $table->dropColumn('moderation_notes');
            }
        });
    }
};
