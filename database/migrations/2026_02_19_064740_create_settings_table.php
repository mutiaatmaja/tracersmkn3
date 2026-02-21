<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel untuk menyimpan pengaturan aplikasi
     * Seperti informasi sekolah, konfigurasi tracer, dll
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Kunci pengaturan (nama unik untuk setiap setting)
            // Contoh: school_name, school_address, tracer_frequency, dll
            $table->string('key')->unique();

            // Nilai setting dalam format JSON untuk fleksibilitas
            $table->json('value')->nullable();

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel settings
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
