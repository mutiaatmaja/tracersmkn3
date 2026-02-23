<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\City;
use App\Models\Province;
use App\Models\TracerSubmission;
use Illuminate\Database\Seeder;

class TracerSubmissionSeeder extends Seeder
{
    /**
     * Seed dummy data isian tracer study.
     */
    public function run(): void
    {
        $alumnis = Alumni::query()->orderBy('id')->get();

        if ($alumnis->isEmpty()) {
            return;
        }

        $periodeTahunIni = now()->year;
        $periodeTahunLalu = now()->subYear()->year;

        $defaultProvinceId = Province::query()->value('id');
        $defaultCityId = $defaultProvinceId
            ? City::query()->where('province_id', $defaultProvinceId)->value('id')
            : null;

        foreach ($alumnis as $index => $alumni) {
            $profileIndex = $index % 5;

            TracerSubmission::query()->updateOrCreate(
                [
                    'alumni_id' => $alumni->id,
                    'periode_tahun' => $periodeTahunIni,
                ],
                $this->buildPayload(
                    profileIndex: $profileIndex,
                    status: 'submitted',
                    alumni: $alumni,
                    provinceId: $defaultProvinceId,
                    cityId: $defaultCityId,
                    periodYear: $periodeTahunIni,
                ),
            );

            if ($index % 2 === 0) {
                TracerSubmission::query()->updateOrCreate(
                    [
                        'alumni_id' => $alumni->id,
                        'periode_tahun' => $periodeTahunLalu,
                    ],
                    $this->buildPayload(
                        profileIndex: ($profileIndex + 2) % 5,
                        status: $index % 4 === 0 ? 'draft' : 'submitted',
                        alumni: $alumni,
                        provinceId: $defaultProvinceId,
                        cityId: $defaultCityId,
                        periodYear: $periodeTahunLalu,
                    ),
                );
            }
        }
    }

    private function buildPayload(int $profileIndex, string $status, Alumni $alumni, ?int $provinceId, ?int $cityId, int $periodYear): array
    {
        $isSubmitted = $status === 'submitted';
        $isIndonesia = $profileIndex !== 4;
        $negara = $isIndonesia ? 'Indonesia' : 'Malaysia';

        $base = [
            'status' => $status,
            'submitted_at' => $isSubmitted ? now()->subDays($profileIndex + 1) : null,
            'a1_status_perkawinan' => ['belum_menikah', 'sudah_menikah', 'cerai'][$profileIndex % 3],
            'a2_negara_tinggal' => $negara,
            'a3_provinsi_id' => $isIndonesia ? $provinceId : null,
            'a4_kota_id' => $isIndonesia ? $cityId : null,
            'a5_email_aktif' => $alumni->email_pribadi ?: 'alumni'.$alumni->id.'@example.test',
            'a6_no_hp' => $alumni->nomor_telepon ?: '0812'.str_pad((string) $alumni->id, 8, '0', STR_PAD_LEFT),
            'b4_penghasilan_min_1jam' => $profileIndex !== 2,
            'b5_membantu_usaha' => in_array($profileIndex, [0, 3], true),
            'b6_sementara_tidak_bekerja' => $profileIndex === 2,
            'g1_alasan_pilih_smk' => $profileIndex === 1
                ? ['keinginan_sendiri', 'cepat_kerja']
                : ['cepat_kerja', 'keinginan_orangtua'],
            'g1_alasan_lainnya' => null,
            'g2_durasi_pkl' => ['kurang_6_bulan', '6_bulan', 'lebih_6_bulan'][$profileIndex % 3],
            'g3_kualitas_pkl' => ['baik', 'sangat_baik', 'tidak_baik', 'baik', 'sangat_baik'][$profileIndex],
            'g4_kesesuaian_pkl' => ['baik', 'sangat_baik', 'tidak_baik', 'baik', 'sangat_baik'][$profileIndex],
        ];

        return match ($profileIndex) {
            0 => array_merge($base, $this->profileBekerjaWirausaha($periodYear)),
            1 => array_merge($base, $this->profileStudiLanjut($periodYear)),
            2 => array_merge($base, $this->profileBelumBekerja()),
            3 => array_merge($base, $this->profileBekerjaKaryawan($periodYear)),
            default => array_merge($base, $this->profileBekerjaDanStudi($periodYear)),
        };
    }

    private function profileBekerjaWirausaha(int $periodYear): array
    {
        return [
            'b1_studi_lanjut' => false,
            'b2_bekerja' => true,
            'b3_bentuk_pekerjaan' => 'wirausaha_pekerja_dibayar',
            'c1_waktu_pekerjaan_pertama' => 'setelah_lulus',
            'c2_lokasi_kerja' => 'dalam_negeri',
            'c3_jabatan' => 'Owner',
            'c4_nama_perusahaan' => 'Usaha Mandiri '.$periodYear,
            'c5_nama_atasan' => 'Diri Sendiri',
            'c6_jabatan_atasan' => 'Pemilik',
            'c7_kontak_atasan' => '081200000001',
            'c8_jenis_instansi' => 'usaha_perorangan',
            'c9_jam_kerja_per_minggu' => 48,
            'c10_penghasilan_bulanan' => '3_5_juta',
            'c11_frekuensi_ganti_kerja' => 'satu_kali',
            'c12_alasan_ganti_kerja' => null,
            'c12_alasan_lainnya' => null,
            'c13_cara_dapat_kerja' => ['ikatan_alumni', 'bantuan_orang_lain'],
            'c13_cara_lainnya' => null,
            'c14_kesesuaian_pekerjaan' => 'selaras',
            'd1_lokasi_studi' => null,
            'd2_jenjang' => null,
            'd3_nama_pt' => null,
            'd4_program_studi' => null,
            'd5_kesesuaian_studi' => null,
            'd6_mulai_studi' => null,
            'd7_alasan_lanjut' => null,
            'd7_alasan_lainnya' => null,
            'e1_aktivitas_mingguan' => null,
            'e2_aktivitas_cari_kerja' => null,
            'e3_lama_cari_bulan' => null,
            'e4_alasan_mencari' => null,
            'f1_lokasi_usaha' => 'dalam_negeri',
            'f2_bentuk_usaha' => 'perorangan',
            'f2_bentuk_usaha_lainnya' => null,
            'f3_bidang_usaha' => 'Kuliner',
            'f4_produk_usaha' => 'barang_jasa',
            'f5_kepemilikan' => 'milik_sendiri',
            'f6_mulai_usaha' => now()->setYear($periodYear)->startOfYear()->addMonths(3)->toDateString(),
            'f7_omset_bulanan' => '25_50_jt',
            'f8_riwayat_ganti_usaha' => '1x',
        ];
    }

    private function profileStudiLanjut(int $periodYear): array
    {
        return [
            'b1_studi_lanjut' => true,
            'b2_bekerja' => false,
            'b3_bentuk_pekerjaan' => null,
            'c1_waktu_pekerjaan_pertama' => null,
            'c2_lokasi_kerja' => null,
            'c3_jabatan' => null,
            'c4_nama_perusahaan' => null,
            'c5_nama_atasan' => null,
            'c6_jabatan_atasan' => null,
            'c7_kontak_atasan' => null,
            'c8_jenis_instansi' => null,
            'c9_jam_kerja_per_minggu' => null,
            'c10_penghasilan_bulanan' => null,
            'c11_frekuensi_ganti_kerja' => null,
            'c12_alasan_ganti_kerja' => null,
            'c12_alasan_lainnya' => null,
            'c13_cara_dapat_kerja' => null,
            'c13_cara_lainnya' => null,
            'c14_kesesuaian_pekerjaan' => null,
            'd1_lokasi_studi' => 'dalam_negeri',
            'd2_jenjang' => 's1',
            'd3_nama_pt' => 'Universitas Negeri Pontianak',
            'd4_program_studi' => 'Teknik Informatika',
            'd5_kesesuaian_studi' => 'selaras',
            'd6_mulai_studi' => now()->setYear($periodYear)->startOfYear()->addMonths(7)->toDateString(),
            'd7_alasan_lanjut' => ['meningkatkan_kompetensi', 'status_sosial'],
            'd7_alasan_lainnya' => null,
            'e1_aktivitas_mingguan' => null,
            'e2_aktivitas_cari_kerja' => null,
            'e3_lama_cari_bulan' => null,
            'e4_alasan_mencari' => null,
            'f1_lokasi_usaha' => null,
            'f2_bentuk_usaha' => null,
            'f2_bentuk_usaha_lainnya' => null,
            'f3_bidang_usaha' => null,
            'f4_produk_usaha' => null,
            'f5_kepemilikan' => null,
            'f6_mulai_usaha' => null,
            'f7_omset_bulanan' => null,
            'f8_riwayat_ganti_usaha' => null,
        ];
    }

    private function profileBelumBekerja(): array
    {
        return [
            'b1_studi_lanjut' => false,
            'b2_bekerja' => false,
            'b3_bentuk_pekerjaan' => null,
            'c1_waktu_pekerjaan_pertama' => null,
            'c2_lokasi_kerja' => null,
            'c3_jabatan' => null,
            'c4_nama_perusahaan' => null,
            'c5_nama_atasan' => null,
            'c6_jabatan_atasan' => null,
            'c7_kontak_atasan' => null,
            'c8_jenis_instansi' => null,
            'c9_jam_kerja_per_minggu' => null,
            'c10_penghasilan_bulanan' => null,
            'c11_frekuensi_ganti_kerja' => null,
            'c12_alasan_ganti_kerja' => null,
            'c12_alasan_lainnya' => null,
            'c13_cara_dapat_kerja' => null,
            'c13_cara_lainnya' => null,
            'c14_kesesuaian_pekerjaan' => null,
            'd1_lokasi_studi' => null,
            'd2_jenjang' => null,
            'd3_nama_pt' => null,
            'd4_program_studi' => null,
            'd5_kesesuaian_studi' => null,
            'd6_mulai_studi' => null,
            'd7_alasan_lanjut' => null,
            'd7_alasan_lainnya' => null,
            'e1_aktivitas_mingguan' => ['pelatihan', 'organisasi_sosial'],
            'e2_aktivitas_cari_kerja' => ['kirim_lamaran', 'ikut_seleksi'],
            'e3_lama_cari_bulan' => 5,
            'e4_alasan_mencari' => ['tidak_lanjut_kuliah', 'upah_kurang'],
            'f1_lokasi_usaha' => null,
            'f2_bentuk_usaha' => null,
            'f2_bentuk_usaha_lainnya' => null,
            'f3_bidang_usaha' => null,
            'f4_produk_usaha' => null,
            'f5_kepemilikan' => null,
            'f6_mulai_usaha' => null,
            'f7_omset_bulanan' => null,
            'f8_riwayat_ganti_usaha' => null,
        ];
    }

    private function profileBekerjaKaryawan(int $periodYear): array
    {
        return [
            'b1_studi_lanjut' => false,
            'b2_bekerja' => true,
            'b3_bentuk_pekerjaan' => 'buruh_karyawan_pegawai',
            'c1_waktu_pekerjaan_pertama' => 'sebelum_lulus',
            'c2_lokasi_kerja' => 'dalam_negeri',
            'c3_jabatan' => 'Staff Operasional',
            'c4_nama_perusahaan' => 'PT Maju Bersama '.$periodYear,
            'c5_nama_atasan' => 'Budi Santoso',
            'c6_jabatan_atasan' => 'Supervisor',
            'c7_kontak_atasan' => '081200000003',
            'c8_jenis_instansi' => 'perusahaan_swasta_bumn_bumd',
            'c9_jam_kerja_per_minggu' => 45,
            'c10_penghasilan_bulanan' => '3_5_juta',
            'c11_frekuensi_ganti_kerja' => 'dua_kali',
            'c12_alasan_ganti_kerja' => 'karir_sulit',
            'c12_alasan_lainnya' => null,
            'c13_cara_dapat_kerja' => ['bkk_smk', 'job_fair'],
            'c13_cara_lainnya' => null,
            'c14_kesesuaian_pekerjaan' => 'sangat_selaras',
            'd1_lokasi_studi' => null,
            'd2_jenjang' => null,
            'd3_nama_pt' => null,
            'd4_program_studi' => null,
            'd5_kesesuaian_studi' => null,
            'd6_mulai_studi' => null,
            'd7_alasan_lanjut' => null,
            'd7_alasan_lainnya' => null,
            'e1_aktivitas_mingguan' => null,
            'e2_aktivitas_cari_kerja' => null,
            'e3_lama_cari_bulan' => null,
            'e4_alasan_mencari' => null,
            'f1_lokasi_usaha' => null,
            'f2_bentuk_usaha' => null,
            'f2_bentuk_usaha_lainnya' => null,
            'f3_bidang_usaha' => null,
            'f4_produk_usaha' => null,
            'f5_kepemilikan' => null,
            'f6_mulai_usaha' => null,
            'f7_omset_bulanan' => null,
            'f8_riwayat_ganti_usaha' => null,
        ];
    }

    private function profileBekerjaDanStudi(int $periodYear): array
    {
        return [
            'b1_studi_lanjut' => true,
            'b2_bekerja' => true,
            'b3_bentuk_pekerjaan' => 'pekerja_bebas',
            'c1_waktu_pekerjaan_pertama' => 'setelah_lulus',
            'c2_lokasi_kerja' => 'luar_negeri',
            'c3_jabatan' => 'Freelance Designer',
            'c4_nama_perusahaan' => 'Global Remote Team',
            'c5_nama_atasan' => 'Anna Kim',
            'c6_jabatan_atasan' => 'Team Lead',
            'c7_kontak_atasan' => 'annateam@example.com',
            'c8_jenis_instansi' => 'usaha_perorangan',
            'c9_jam_kerja_per_minggu' => 35,
            'c10_penghasilan_bulanan' => 'lebih_5_juta',
            'c11_frekuensi_ganti_kerja' => 'satu_kali',
            'c12_alasan_ganti_kerja' => null,
            'c12_alasan_lainnya' => null,
            'c13_cara_dapat_kerja' => ['iklan', 'mitra_smk'],
            'c13_cara_lainnya' => null,
            'c14_kesesuaian_pekerjaan' => 'selaras',
            'd1_lokasi_studi' => 'luar_negeri',
            'd2_jenjang' => 'd4',
            'd3_nama_pt' => 'Global Polytechnic Institute',
            'd4_program_studi' => 'Digital Media',
            'd5_kesesuaian_studi' => 'selaras',
            'd6_mulai_studi' => now()->setYear($periodYear)->startOfYear()->addMonths(8)->toDateString(),
            'd7_alasan_lanjut' => ['beasiswa', 'meningkatkan_kompetensi'],
            'd7_alasan_lainnya' => null,
            'e1_aktivitas_mingguan' => null,
            'e2_aktivitas_cari_kerja' => null,
            'e3_lama_cari_bulan' => null,
            'e4_alasan_mencari' => null,
            'f1_lokasi_usaha' => null,
            'f2_bentuk_usaha' => null,
            'f2_bentuk_usaha_lainnya' => null,
            'f3_bidang_usaha' => null,
            'f4_produk_usaha' => null,
            'f5_kepemilikan' => null,
            'f6_mulai_usaha' => null,
            'f7_omset_bulanan' => null,
            'f8_riwayat_ganti_usaha' => null,
        ];
    }
}
