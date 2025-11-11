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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name', 255);
            $table->text('task_description')->nullable();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->cascadeOnDelete()->comment('For sub-tasks hierarchy');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 50)->default('Pending')->comment("'Pending', 'In Progress', 'Completed', 'Delayed', 'Cancelled'");
            $table->string('progress_type', 50)->default('Status')->comment("'Status', 'Target'");
            $table->string('priority', 20)->default('Medium')->comment("'Low', 'Medium', 'High', 'Urgent'");
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('estimated_budget', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
