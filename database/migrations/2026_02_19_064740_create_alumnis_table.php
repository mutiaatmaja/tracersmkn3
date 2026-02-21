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
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('nama_lengkap')->nullable();
            $table->string('nisn')->nullable()->unique();
            $table->string('nik')->nullable()->unique();
            $table->foreignId('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->year('tahun_lulus');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email_pribadi')->nullable();
            $table->date('last_tracer_date')->nullable();
            $table->date('next_tracer_eligible_date')->nullable();
            $table->string('foto_profil')->nullable();
            $table->string('link_media_sosial')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
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
