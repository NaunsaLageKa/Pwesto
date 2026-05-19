<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy installs may have a partial `disputes` table (create migration skipped).
     * Add any columns the app expects but the DB is missing.
     */
    public function up(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        Schema::table('disputes', function (Blueprint $table) {
            if (! Schema::hasColumn('disputes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'hub_owner_id')) {
                $table->unsignedBigInteger('hub_owner_id')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'title')) {
                $table->string('title', 255)->nullable();
            }
            if (! Schema::hasColumn('disputes', 'type')) {
                $table->enum('type', ['payment', 'service', 'behavior', 'other'])->default('other');
            }
            if (! Schema::hasColumn('disputes', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'evidence')) {
                $table->text('evidence')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'status')) {
                $table->enum('status', ['open', 'resolved', 'escalated'])->default('open');
            }
            if (! Schema::hasColumn('disputes', 'resolution')) {
                $table->text('resolution')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'resolved_by')) {
                $table->unsignedBigInteger('resolved_by')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable();
            }
            if (! Schema::hasColumn('disputes', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('disputes')) {
            return;
        }

        $optional = [
            'user_id',
            'hub_owner_id',
            'booking_id',
            'title',
            'type',
            'description',
            'evidence',
            'status',
            'resolution',
            'resolved_by',
            'resolved_at',
            'escalated_at',
            'created_by',
        ];

        $toDrop = [];
        foreach ($optional as $col) {
            if (Schema::hasColumn('disputes', $col)) {
                $toDrop[] = $col;
            }
        }

        if ($toDrop !== []) {
            Schema::table('disputes', function (Blueprint $table) use ($toDrop) {
                $table->dropColumn($toDrop);
            });
        }
    }
};
