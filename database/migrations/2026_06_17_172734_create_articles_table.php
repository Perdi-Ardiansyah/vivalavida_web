<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penulis')->nullable();
            $table->enum('kategori', ['Berita', 'Promo', 'Event'])->default('Berita');
            $table->text('konten')->nullable();
            $table->string('gambar')->nullable();
            $table->enum('status', ['Publish', 'Draft'])->default('Publish');
            $table->integer('views')->default(0); // Untuk metrik analitik
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};