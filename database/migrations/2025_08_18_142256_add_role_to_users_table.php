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
        Schema::table('users', function (Blueprint $table) {
            /**
             * Add a 'role' column to the 'users' table.
             * This column will store the role of the user, defaulting to 'user'.
             * Role values can be 'admin', 'editor', or 'user'.
             */
            $table->string('role')->default('user')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /**
             * Drop the 'role' column from the 'users' table.
             */
            $table->dropColumn('role');
        });
    }
};
