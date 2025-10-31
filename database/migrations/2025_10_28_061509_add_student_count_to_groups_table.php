<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'supervisor_id')) {
                $table->unsignedBigInteger('supervisor_id')->nullable()->after('faculty');
                $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('groups', 'student_count')) {
                $table->integer('student_count')->default(0)->after('supervisor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'student_count')) {
                $table->dropColumn('student_count');
            }
            if (Schema::hasColumn('groups', 'supervisor_id')) {
                $table->dropForeign(['supervisor_id']);
                $table->dropColumn('supervisor_id');
            }
        });
    }
};
