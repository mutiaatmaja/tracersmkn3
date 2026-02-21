<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel study_programs untuk menyimpan data program studi.
     */
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('university_id')->constrained('universities')->cascadeOnDelete();
            $table->string('kode', 20)->unique()->comment('Kode program studi');
            $table->string('nama')->comment('Nama program studi');
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel study_programs.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_programs');
    }
};
