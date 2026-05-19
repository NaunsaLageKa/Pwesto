<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy databases may use VARCHAR(5) or an ENUM without `escalated`, causing truncation when saving status.
     */
    public function up(): void
    {
        if (! Schema::hasTable('disputes') || ! Schema::hasColumn('disputes', 'status')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `disputes` MODIFY `status` ENUM('open', 'resolved', 'escalated') NOT NULL DEFAULT 'open'");
        }
    }

    /**
     * Intentionally empty: shrinking the column could break rows already escalated.
     */
    public function down(): void
    {
        //
    }
};
