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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique()->comment('Nomor Induk Kependudukan');
            $table->string('kk_number', 16)->nullable()->comment('Nomor Kartu Keluarga');
            $table->string('full_name');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->text('address')->nullable();
            $table->string('rt', 10)->nullable()->comment('Rukun Tetangga');
            $table->string('rw', 10)->nullable()->comment('Rukun Warga');
            $table->string('dusun', 50)->nullable()->comment('Nama Dusun');
            $table->string('religion', 50)->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('occupation')->nullable();
            $table->string('education', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('is_head')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('kk_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
