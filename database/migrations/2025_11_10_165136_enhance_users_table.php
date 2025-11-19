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
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name', 150)->after('id');
            $table->string('username', 50)->unique()->after('full_name');
            $table->dropColumn('name');
            $table->string('role', 50)->after('password')->comment("'admin', 'operator', 'kepala_desa', 'staff', 'kasun'");
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('position', 20)->nullable()->after('phone');
            $table->string('dusun')->nullable()->after('position');
            $table->boolean('is_active')->default(true)->after('dusun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'username', 'role', 'phone', 'is_active']);
            $table->string('name', 255)->after('id');
        });
    }
};
