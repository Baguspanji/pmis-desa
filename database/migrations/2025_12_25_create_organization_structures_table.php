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
        Schema::create('organization_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->enum('organization_type', ['Pemerintah Desa', 'Badan Permasyarakatan Desa']);
            $table->enum('level', ['head', 'vice', 'staff', 'member'])->default('staff');
            $table->foreignId('parent_id')->nullable()->constrained('organization_structures')->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_structures');
    }
};
