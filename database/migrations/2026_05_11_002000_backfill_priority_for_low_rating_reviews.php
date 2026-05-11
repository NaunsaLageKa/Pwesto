<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Back-fill the `priority` column for any existing review whose rating is
     * 1 or 2 stars. The application now treats those as automatic
     * High Priority complaints, so historical rows should match the new rule.
     */
    public function up(): void
    {
        if (!Schema::hasTable('reviews') || !Schema::hasColumn('reviews', 'priority')) {
            return;
        }

        DB::table('reviews')
            ->where('rating', '<=', 2)
            ->where(function ($q) {
                $q->whereNull('priority')->orWhere('priority', '!=', 1);
            })
            ->update(['priority' => 1]);
    }

    /**
     * We intentionally do NOT undo the back-fill on rollback, because:
     *   1. We cannot tell which rows were originally priority=0 vs priority=1.
     *   2. Rolling back to priority=0 would silently demote legitimate
     *      complaints in the moderation queue.
     */
    public function down(): void
    {
    }
};
