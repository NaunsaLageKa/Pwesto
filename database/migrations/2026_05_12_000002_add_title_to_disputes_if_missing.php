<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some legacy schemas require a non-null `title`; newer aligned schemas may omit it.
     */
    public function up(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'title')) {
                $table->string('title', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (Schema::hasColumn('disputes', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
