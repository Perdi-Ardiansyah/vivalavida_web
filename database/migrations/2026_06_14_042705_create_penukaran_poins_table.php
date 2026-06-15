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
        Schema::create('penukaran_poins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('aturan_poin_id')->constrained('aturan_poins')->cascadeOnDelete();
            $table->integer('poin_ditukar');
            $table->integer('jumlah_voucher')->default(1);
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('disetujui');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penukaran_poins');
    }
};
