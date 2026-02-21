<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel universities untuk menyimpan data perguruan tinggi.
     */
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique()->comment('Kode perguruan tinggi');
            $table->string('nama')->comment('Nama perguruan tinggi');
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel universities.
     */
    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
