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
        Schema::create('voucher_diskons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penukaran_id')->nullable()->constrained('penukaran_poins')->nullOnDelete();
            $table->enum('tipe_diskon', ['nominal', 'persen']);
            $table->decimal('nilai_diskon', 10, 2);
            $table->string('kode', 50)->unique();
            $table->enum('status', ['aktif', 'dipakai', 'kadaluarsa'])->default('aktif');
            $table->date('berlaku_hingga');
            $table->timestamp('dipakai_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_diskons');
    }
};
