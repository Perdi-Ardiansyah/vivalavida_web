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
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_name'); // cth: iPhone 15 Pro, Chrome Windows
            $table->string('location')->nullable(); // cth: Bandung, Indonesia
            $table->string('ip_address')->nullable();
            $table->boolean('is_active')->default(true); // Untuk menandai sesi saat ini
            $table->timestamps();
        });
    }
};
