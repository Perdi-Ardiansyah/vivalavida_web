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
        Schema::table('pesanans', function (Blueprint $table) {
            // Melacak status spesifik di dapur makanan
            $table->enum('status_makanan', ['menunggu', 'sedang_dimasak', 'selesai'])->default('menunggu')->after('status_dapur');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn('status_makanan');
        });
    }
};
