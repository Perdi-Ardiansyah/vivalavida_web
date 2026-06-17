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
            // Menambahkan kolom baru setelah kolom phone
            $table->string('tanggal_lahir')->nullable()->after('phone');
            $table->string('jenis_kelamin')->nullable()->after('tanggal_lahir');
            $table->string('instagram')->nullable()->after('jenis_kelamin');
            $table->string('kopi_favorit')->nullable()->after('instagram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'jenis_kelamin', 'instagram', 'kopi_favorit']);
        });
    }
};