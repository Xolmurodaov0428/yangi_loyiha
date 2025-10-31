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
        // Create tasks table
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->dateTime('due_date');
                $table->string('status')->default('pending');
                $table->string('file_path')->nullable();
                $table->foreignId('supervisor_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create student_task pivot table
        if (!Schema::hasTable('student_task')) {
            Schema::create('student_task', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('task_id')->constrained()->onDelete('cascade');
                $table->string('status')->default('pending');
                $table->string('file_path')->nullable();
                $table->integer('score')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                
                // Ensure each student can only be assigned to a task once
                $table->unique(['student_id', 'task_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_task');
        Schema::dropIfExists('tasks');
    }
};
