<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Mengecek dan menambahkan kolom nama_pelanggan jika belum ada
            if (!Schema::hasColumn('pesanans', 'nama_pelanggan')) {
                $table->string('nama_pelanggan')->nullable()->after('user_id');
            }
            
            // Mengecek dan menambahkan kolom sumber_pesanan jika belum ada
            if (!Schema::hasColumn('pesanans', 'sumber_pesanan')) {
                $table->string('sumber_pesanan')->default('app')->after('nama_pelanggan');
            }

            // Mengecek dan menambahkan kolom tipe_pesanan jika belum ada
            if (!Schema::hasColumn('pesanans', 'tipe_pesanan')) {
                $table->string('tipe_pesanan')->default('dine_in')->after('sumber_pesanan');
            }
            
            // Mengubah kolom user_id agar boleh kosong (nullable) untuk pelanggan guest
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pesanans', 'nama_pelanggan')) {
                $table->dropColumn('nama_pelanggan');
            }
            if (Schema::hasColumn('pesanans', 'sumber_pesanan')) {
                $table->dropColumn('sumber_pesanan');
            }
            // Kita tidak me-rollback user_id agar aman
        });
    }
};