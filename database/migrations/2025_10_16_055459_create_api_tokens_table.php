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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Token nomi
            $table->string('token', 64)->unique(); // Token qiymati
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin ID
            $table->boolean('is_active')->default(true); // Faol/nofaol
            $table->timestamp('last_used_at')->nullable(); // Oxirgi ishlatilgan vaqt
            $table->timestamp('expires_at')->nullable(); // Amal qilish muddati
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
