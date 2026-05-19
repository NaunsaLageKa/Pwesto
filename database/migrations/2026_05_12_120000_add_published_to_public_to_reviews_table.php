<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * MySQL 8 rejects FK when column types do not exactly match legacy users.id (e.g. INT vs BIGINT).
     * Store publisher id without a DB FK; integrity is enforced in application code.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'published_to_public_at')) {
                $after = Schema::hasColumn('reviews', 'approved_by') ? 'approved_by' : null;
                if ($after) {
                    $table->timestamp('published_to_public_at')->nullable()->after($after);
                } else {
                    $table->timestamp('published_to_public_at')->nullable();
                }
            }
        });

        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'published_to_public_by')) {
                if (Schema::hasColumn('reviews', 'published_to_public_at')) {
                    $table->unsignedBigInteger('published_to_public_by')->nullable()->after('published_to_public_at');
                } else {
                    $table->unsignedBigInteger('published_to_public_by')->nullable();
                }
            }
        });

        // Remove broken FK if a previous failed attempt left it (unlikely without column).
        $this->dropPublishedByForeignIfExists();
    }

    public function down(): void
    {
        $this->dropPublishedByForeignIfExists();

        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'published_to_public_by')) {
                $table->dropColumn('published_to_public_by');
            }
            if (Schema::hasColumn('reviews', 'published_to_public_at')) {
                $table->dropColumn('published_to_public_at');
            }
        });
    }

    private function dropPublishedByForeignIfExists(): void
    {
        $db = Schema::getConnection()->getDatabaseName();
        $row = DB::selectOne(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ? AND CONSTRAINT_NAME = ?',
            [$db, 'reviews', 'FOREIGN KEY', 'reviews_published_to_public_by_foreign']
        );
        if ($row) {
            DB::statement('ALTER TABLE `reviews` DROP FOREIGN KEY `reviews_published_to_public_by_foreign`');
        }
    }
};
