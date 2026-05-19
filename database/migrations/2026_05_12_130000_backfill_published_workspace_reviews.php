<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Existing approved workspace reviews were visible on Location/welcome before
     * `published_to_public_at` existed. Backfill so they stay public; new rows still
     * start unpublished until an admin uses "Publish to Location".
     */
    public function up(): void
    {
        if (! Schema::hasColumn('reviews', 'published_to_public_at')) {
            return;
        }

        DB::table('reviews')
            ->where('status', 'approved')
            ->where('feedback_type', 'workspace')
            ->whereNull('published_to_public_at')
            ->update([
                'published_to_public_at' => DB::raw('COALESCE(approved_at, created_at)'),
                'published_to_public_by' => DB::raw('approved_by'),
            ]);
    }

    public function down(): void
    {
        // Cannot safely distinguish backfilled rows from admin-published-only rows; no-op.
    }
};
