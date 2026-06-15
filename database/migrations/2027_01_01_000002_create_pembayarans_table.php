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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->enum('metode', ['cash', 'qris', 'transfer', 'gopay']);
            $table->enum('status', ['unpaid', 'paid', 'failed'])->default('unpaid');
            $table->decimal('jumlah_bayar', 10, 2)->default(0);
            $table->decimal('jumlah_kembali', 10, 2)->default(0);
            $table->string('bukti')->nullable(); // Path gambar bukti transfer jika ada
            $table->timestamp('dibayar_at')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
