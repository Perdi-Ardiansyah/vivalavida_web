<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->integer('pajak_persen')->default(11);
            // Nanti kamu bisa tambah kolom lain di sini, misal: biaya_layanan, dll.
            $table->timestamps();
        });

        // Langsung isikan data default 1 baris agar API tidak error
        DB::table('pengaturans')->insert([
            'pajak_persen' => 11,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};