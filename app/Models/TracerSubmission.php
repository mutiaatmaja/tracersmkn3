<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TracerSubmission extends Model
{
    protected $fillable = [
        'alumni_id',
        'periode_tahun',
        'status',
        'submitted_at',
        'a1_status_perkawinan',
        'a2_negara_tinggal',
        'a3_provinsi_id',
        'a4_kota_id',
        'a5_email_aktif',
        'a6_no_hp',
        'b1_studi_lanjut',
        'b2_bekerja',
        'b3_bentuk_pekerjaan',
        'b4_penghasilan_min_1jam',
        'b5_membantu_usaha',
        'b6_sementara_tidak_bekerja',
        'c1_waktu_pekerjaan_pertama',
        'c2_lokasi_kerja',
        'c3_jabatan',
        'c4_nama_perusahaan',
        'c5_nama_atasan',
        'c6_jabatan_atasan',
        'c7_kontak_atasan',
        'c8_jenis_instansi',
        'c9_jam_kerja_per_minggu',
        'c10_penghasilan_bulanan',
        'c11_frekuensi_ganti_kerja',
        'c12_alasan_ganti_kerja',
        'c12_alasan_lainnya',
        'c13_cara_dapat_kerja',
        'c13_cara_lainnya',
        'c14_kesesuaian_pekerjaan',
        'd1_lokasi_studi',
        'd2_jenjang',
        'd3_nama_pt',
        'd4_program_studi',
        'd5_kesesuaian_studi',
        'd6_mulai_studi',
        'd7_alasan_lanjut',
        'd7_alasan_lainnya',
        'e1_aktivitas_mingguan',
        'e2_aktivitas_cari_kerja',
        'e3_lama_cari_bulan',
        'e4_alasan_mencari',
        'f1_lokasi_usaha',
        'f2_bentuk_usaha',
        'f2_bentuk_usaha_lainnya',
        'f3_bidang_usaha',
        'f4_produk_usaha',
        'f5_kepemilikan',
        'f6_mulai_usaha',
        'f7_omset_bulanan',
        'f8_riwayat_ganti_usaha',
        'g1_alasan_pilih_smk',
        'g1_alasan_lainnya',
        'g2_durasi_pkl',
        'g3_kualitas_pkl',
        'g4_kesesuaian_pkl',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'b1_studi_lanjut' => 'boolean',
        'b2_bekerja' => 'boolean',
        'b4_penghasilan_min_1jam' => 'boolean',
        'b5_membantu_usaha' => 'boolean',
        'b6_sementara_tidak_bekerja' => 'boolean',
        'd6_mulai_studi' => 'date',
        'f6_mulai_usaha' => 'date',
        'c13_cara_dapat_kerja' => 'array',
        'd7_alasan_lanjut' => 'array',
        'e1_aktivitas_mingguan' => 'array',
        'e2_aktivitas_cari_kerja' => 'array',
        'e4_alasan_mencari' => 'array',
        'g1_alasan_pilih_smk' => 'array',
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }
}
