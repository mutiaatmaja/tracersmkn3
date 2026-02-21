<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel untuk menyimpan data lengkap alumni
     * Terpisah dari tabel users untuk pemisahan concerns (users untuk auth, alumni untuk data alumni)
     */
    public function up(): void
    {
        Schema::create('alumnis', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key ke users table (one-to-one relationship)
            // Alumni harus terhubung dengan user untuk login
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // NISN (Nomor Induk Siswa Nasional) - untuk verifikasi alumni
            $table->string('nisn')->unique();

            // NIK (Nomor Induk Kependudukan) - alternatif verifikasi
            $table->string('nik')->unique()->nullable();

            // Foreign key ke kompetensi/jurusan saat sekolah
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();

            // Tahun kelulusan
            $table->year('tahun_lulus');

            // Jenis kelamin
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);

            // Tanggal pengisian tracer terakhir
            $table->date('last_tracer_date')->nullable();

            // Tanggal saat alumni bisa isi tracer berikutnya
            $table->date('next_tracer_eligible_date')->nullable();

            // Path/lokasi foto profil
            $table->string('foto_profil')->nullable();

            // URL media sosial (LinkedIn, GitHub, Instagram, dll)
            $table->string('link_media_sosial')->nullable();

            // Timestamp
            $table->timestamps();
        });
    }

    /**
     * Rollback: hapus tabel alumnis
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
