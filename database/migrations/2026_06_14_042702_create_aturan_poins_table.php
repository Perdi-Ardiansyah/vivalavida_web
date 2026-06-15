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
        Schema::create('aturan_poins', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Contoh: "Tukar 500 Poin = Diskon 15%"
            $table->decimal('point_per_rupiah', 8, 2)->nullable(); // Poin yang didapat tiap transaksi
            $table->decimal('point_per_voucher', 8, 2)->nullable(); // Poin yang dibutuhkan untuk ditukar
            $table->enum('tipe_diskon', ['nominal', 'persen'])->nullable();
            $table->decimal('nilai_diskon', 10, 2)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_poins');
    }
};
