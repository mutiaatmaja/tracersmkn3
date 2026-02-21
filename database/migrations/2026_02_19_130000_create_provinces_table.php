<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel provinces untuk menyimpan data provinsi di Indonesia
     */
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('Kode provinsi');
            $table->string('nama')->comment('Nama provinsi');
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel provinces
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
