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
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto_profil')->nullable(); // Untuk menyimpan path foto
            $table->boolean('is_2fa_enabled')->default(false); // Status 2FA aktif/tidak
            $table->string('two_factor_secret')->nullable(); // Kunci rahasia 2FA
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['foto_profil', 'is_2fa_enabled', 'two_factor_secret']);
        });
    }
};
