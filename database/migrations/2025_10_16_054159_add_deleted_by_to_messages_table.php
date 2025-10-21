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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false)->after('is_read');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null')->after('is_deleted');
            $table->timestamp('deleted_at')->nullable()->after('deleted_by');
            $table->boolean('is_edited')->default(false)->after('deleted_at');
            $table->timestamp('edited_at')->nullable()->after('is_edited');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['is_deleted', 'deleted_by', 'deleted_at', 'is_edited', 'edited_at']);
        });
    }
};
