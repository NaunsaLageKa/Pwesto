<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'hub_owner_response')) {
                $table->text('hub_owner_response')->nullable()->after('moderation_notes');
            }

            if (!Schema::hasColumn('reviews', 'hub_owner_responded_at')) {
                $table->timestamp('hub_owner_responded_at')->nullable()->after('hub_owner_response');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'hub_owner_responded_at')) {
                $table->dropColumn('hub_owner_responded_at');
            }

            if (Schema::hasColumn('reviews', 'hub_owner_response')) {
                $table->dropColumn('hub_owner_response');
            }
        });
    }
};
