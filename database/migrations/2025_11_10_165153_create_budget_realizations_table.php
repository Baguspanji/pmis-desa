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
        Schema::create('budget_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('description', 255)->nullable();
            $table->string('category', 100)->nullable()->comment("e.g., 'Material', 'Labor', 'Equipment', 'Administrative'");
            $table->string('transaction_type', 50)->default('Expense')->comment("'Income', 'Expense'");
            $table->date('transaction_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_realizations');
    }
};
