<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategori_menus', function (Blueprint $table) {
            // Menambahkan tipe untuk mengelompokkan tujuan produksi
            $table->enum('tipe', ['makanan', 'minuman'])->default('minuman')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_menus', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
