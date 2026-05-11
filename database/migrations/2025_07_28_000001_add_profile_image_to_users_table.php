<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * profile_image is created in 0001_01_01_000000_create_users_table.
     * This migration previously added `role`, which duplicated 2025_07_29_071808_add_role_to_users_table
     * and broke fresh installs / PHPUnit (SQLite) with "duplicate column name: role".
     */
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
