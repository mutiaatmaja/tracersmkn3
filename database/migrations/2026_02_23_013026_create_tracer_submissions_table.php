<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
            $table->unsignedSmallInteger('periode_tahun');
            $table->string('status', 20)->default('draft');
            $table->timestamp('submitted_at')->nullable();

            $table->string('a1_status_perkawinan')->nullable();
            $table->string('a2_negara_tinggal')->nullable();
            $table->foreignId('a3_provinsi_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('a4_kota_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('a5_email_aktif')->nullable();
            $table->string('a6_no_hp')->nullable();

            $table->boolean('b1_studi_lanjut')->nullable();
            $table->boolean('b2_bekerja')->nullable();
            $table->string('b3_bentuk_pekerjaan')->nullable();
            $table->boolean('b4_penghasilan_min_1jam')->nullable();
            $table->boolean('b5_membantu_usaha')->nullable();
            $table->boolean('b6_sementara_tidak_bekerja')->nullable();

            $table->string('c1_waktu_pekerjaan_pertama')->nullable();
            $table->string('c2_lokasi_kerja')->nullable();
            $table->string('c3_jabatan')->nullable();
            $table->string('c4_nama_perusahaan')->nullable();
            $table->string('c5_nama_atasan')->nullable();
            $table->string('c6_jabatan_atasan')->nullable();
            $table->string('c7_kontak_atasan')->nullable();
            $table->string('c8_jenis_instansi')->nullable();
            $table->unsignedSmallInteger('c9_jam_kerja_per_minggu')->nullable();
            $table->string('c10_penghasilan_bulanan')->nullable();
            $table->string('c11_frekuensi_ganti_kerja')->nullable();
            $table->string('c12_alasan_ganti_kerja')->nullable();
            $table->string('c12_alasan_lainnya')->nullable();
            $table->json('c13_cara_dapat_kerja')->nullable();
            $table->string('c13_cara_lainnya')->nullable();
            $table->string('c14_kesesuaian_pekerjaan')->nullable();

            $table->string('d1_lokasi_studi')->nullable();
            $table->string('d2_jenjang')->nullable();
            $table->string('d3_nama_pt')->nullable();
            $table->string('d4_program_studi')->nullable();
            $table->string('d5_kesesuaian_studi')->nullable();
            $table->date('d6_mulai_studi')->nullable();
            $table->json('d7_alasan_lanjut')->nullable();
            $table->string('d7_alasan_lainnya')->nullable();

            $table->json('e1_aktivitas_mingguan')->nullable();
            $table->json('e2_aktivitas_cari_kerja')->nullable();
            $table->unsignedSmallInteger('e3_lama_cari_bulan')->nullable();
            $table->json('e4_alasan_mencari')->nullable();

            $table->string('f1_lokasi_usaha')->nullable();
            $table->string('f2_bentuk_usaha')->nullable();
            $table->string('f2_bentuk_usaha_lainnya')->nullable();
            $table->string('f3_bidang_usaha')->nullable();
            $table->string('f4_produk_usaha')->nullable();
            $table->string('f5_kepemilikan')->nullable();
            $table->date('f6_mulai_usaha')->nullable();
            $table->string('f7_omset_bulanan')->nullable();
            $table->string('f8_riwayat_ganti_usaha')->nullable();

            $table->json('g1_alasan_pilih_smk')->nullable();
            $table->string('g1_alasan_lainnya')->nullable();
            $table->string('g2_durasi_pkl')->nullable();
            $table->string('g3_kualitas_pkl')->nullable();
            $table->string('g4_kesesuaian_pkl')->nullable();

            $table->timestamps();

            $table->unique(['alumni_id', 'periode_tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_submissions');
    }
};
