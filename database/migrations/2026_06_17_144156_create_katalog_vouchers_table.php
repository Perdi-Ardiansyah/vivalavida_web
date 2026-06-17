<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('katalog_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->integer('poin_dibutuhkan')->default(0);
            $table->enum('tipe_diskon', ['persen', 'nominal'])->default('persen');
            $table->integer('nilai_diskon');
            $table->string('status')->default('aktif'); // aktif atau expired
            $table->date('berlaku_hingga')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('katalog_vouchers');
    }
};