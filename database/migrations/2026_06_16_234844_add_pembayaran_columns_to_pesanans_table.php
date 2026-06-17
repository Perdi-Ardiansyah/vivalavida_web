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
            // Menambahkan status pembayaran (default: belum bayar)
            $table->enum('status_pembayaran', ['belum_bayar', 'sudah_bayar'])->default('belum_bayar')->after('status');
            
            // Menambahkan metode pembayaran (QRIS, Tunai, Gopay, dll)
            $table->string('metode_pembayaran')->nullable()->after('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['status_pembayaran', 'metode_pembayaran']);
        });
    }
};
