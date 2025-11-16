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
        Schema::create('task_logbooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('task_target_id')->nullable();
            $table->text('title');
            $table->text('description');
            $table->date('log_date');
            $table->enum('log_type', ['progress_update', 'issue', 'milestone', 'meeting', 'field_visit', 'other'])->default('progress_update');
            $table->decimal('progress_value', 10, 2)->nullable()->comment('Progress dalam angka jika ada');
            $table->string('status', 50)->nullable()->comment('Status saat entry dibuat');
            $table->string('location', 255)->nullable()->comment('Lokasi kegiatan jika relevan');
            $table->dateTime('activity_date')->comment('Tanggal & waktu aktivitas terjadi');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->comment('User yang memverifikasi entry log');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Index untuk performa
            $table->index(['task_id', 'activity_date']);
            $table->index(['task_target_id', 'activity_date']);

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('task_target_id')->references('id')->on('task_targets')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_logbooks');
    }
};
