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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'faculty')) {
                $table->string('faculty')->nullable()->after('group_id');
            }

            if (!Schema::hasColumn('students', 'internship_start_date')) {
                $table->date('internship_start_date')->nullable()->after('organization_id');
            }

            if (!Schema::hasColumn('students', 'internship_end_date')) {
                $table->date('internship_end_date')->nullable()->after('internship_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'faculty')) {
                $table->dropColumn('faculty');
            }

            if (Schema::hasColumn('students', 'internship_start_date')) {
                $table->dropColumn('internship_start_date');
            }

            if (Schema::hasColumn('students', 'internship_end_date')) {
                $table->dropColumn('internship_end_date');
            }
        });
    }
};
