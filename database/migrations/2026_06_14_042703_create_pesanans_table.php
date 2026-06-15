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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Nullable jika kasir input tamu tanpa akun
            $table->foreignId('meja_id')->nullable()->constrained('mejas')->nullOnDelete();
            $table->foreignId('alamat_pengiriman_id')->nullable()->constrained('alamat_pelanggans')->nullOnDelete();
            
            $table->enum('tipe_pesanan', ['dine_in', 'takeaway', 'delivery']);
            $table->enum('sumber_pesanan', ['app', 'kasir']);
            $table->enum('status', ['new', 'preparing', 'ready', 'completed', 'cancelled'])->default('new');
            
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->unsignedBigInteger('voucher_id')->nullable(); // Belum dikunci foreignKey agar tidak circular dependency saat migrasi
            $table->decimal('diskon_voucher', 10, 2)->default(0);
            $table->decimal('total_akhir', 10, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
