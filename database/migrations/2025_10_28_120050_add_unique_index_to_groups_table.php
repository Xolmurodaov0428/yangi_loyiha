<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make group name unique per faculty (composite unique index)
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Add composite unique index: name must be unique within each faculty
            $table->unique(['name', 'faculty'], 'groups_name_faculty_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique('groups_name_faculty_unique');
        });
    }
};
