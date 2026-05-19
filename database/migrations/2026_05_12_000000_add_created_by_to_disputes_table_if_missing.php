<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original create_disputes migration no-ops when the table already exists,
     * so older databases may be missing columns such as created_by.
     */
    public function up(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('booking_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};
