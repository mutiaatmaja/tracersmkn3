<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel untuk menyimpan data kompetensi keahlian/jurusan sekolah
     */
    public function up(): void
    {
        Schema::create('competencies', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Kode kompetensi (RPL, TKJ, AKL, BDP, dll)
            $table->string('kode')->unique();

            // Nama kompetensi keahlian
            $table->string('nama');

            // Deskripsi kompetensi
            $table->text('deskripsi')->nullable();

            // Status aktif/nonaktif
            $table->boolean('aktif')->default(true);

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel competencies
     */
    public function down(): void
    {
        Schema::dropIfExists('competencies');
    }
};
