<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel cities untuk menyimpan data kabupaten/kota di Indonesia
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
            $table->string('kode', 10)->unique()->comment('Kode kabupaten/kota');
            $table->string('nama')->comment('Nama kabupaten/kota');
            $table->enum('tipe', ['kabupaten', 'kota'])->comment('Tipe: kabupaten atau kota');
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel cities
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
