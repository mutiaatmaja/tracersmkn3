<?php

namespace App\Exports;

use App\Models\City;
use App\Models\Province;
use App\Models\TracerSubmission;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TracerSubmissionsExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly ?int $periodeTahun = null) {}

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'NIK',
            'Nama',
            'Tahun Lulus',
            'No HP',
            'Tanggal Isi Tracer',
            'A1. Status Perkawinan',
            'A2. Negara Tinggal',
            'A3. Provinsi',
            'A4. Kota/Kabupaten',
            'A5. Email Aktif',
            'A6. Nomor HP Aktif',
            'B1. Studi Lanjut',
            'B2. Bekerja / Berwirausaha',
            'B3. Bentuk Pekerjaan',
            'B4. Minimal 1 jam untuk penghasilan',
            'B5. Membantu usaha keluarga',
            'B6. Sementara tidak bekerja',
            'C1. Waktu mendapatkan pekerjaan pertama',
            'C2. Lokasi kerja',
            'C3. Jabatan',
            'C4. Nama perusahaan',
            'C5. Nama atasan',
            'C6. Jabatan atasan',
            'C7. Kontak atasan',
            'C8. Jenis instansi',
            'C9. Jam kerja per minggu',
            'C10. Penghasilan bulanan',
            'C11. Frekuensi ganti kerja',
            'C12. Alasan ganti kerja',
            'C12. Alasan lainnya',
            'C13. Cara mendapatkan kerja',
            'C13. Cara lainnya',
            'C14. Kesesuaian pekerjaan',
            'D1. Lokasi studi',
            'D2. Jenjang',
            'D3. Nama perguruan tinggi',
            'D4. Program studi',
            'D5. Kesesuaian studi',
            'D6. Mulai studi',
            'D7. Alasan lanjut studi',
            'D7. Alasan lainnya',
            'E1. Aktivitas mingguan',
            'E2. Aktivitas cari kerja',
            'E3. Lama cari kerja (bulan)',
            'E4. Alasan mencari kerja',
            'F1. Lokasi usaha',
            'F2. Bentuk usaha',
            'F2. Bentuk usaha lainnya',
            'F3. Bidang usaha',
            'F4. Produk usaha',
            'F5. Kepemilikan',
            'F6. Mulai usaha',
            'F7. Omset bulanan',
            'F8. Riwayat ganti usaha',
            'G1. Alasan memilih SMK',
            'G1. Alasan lainnya',
            'G2. Durasi PKL',
            'G3. Kualitas PKL',
            'G4. Kesesuaian PKL',
        ];
    }

    public function collection(): Collection
    {
        $query = TracerSubmission::query()->with('alumni');

        if ($this->periodeTahun !== null) {
            $query->where('periode_tahun', $this->periodeTahun);
        }

        $provinceNames = Province::query()->pluck('nama', 'id');
        $cityNames = City::query()->pluck('nama', 'id');

        $singleValueLabels = $this->singleValueLabels();
        $multiValueLabels = $this->multiValueLabels();

        return $query
            ->orderByDesc('periode_tahun')
            ->orderByDesc('updated_at')
            ->get()
            ->values()
            ->map(function (TracerSubmission $submission, int $index) use ($provinceNames, $cityNames, $singleValueLabels, $multiValueLabels): array {
                return [
                    $index + 1,
                    $submission->alumni?->nisn,
                    $submission->alumni?->nik,
                    $submission->alumni?->nama_lengkap,
                    $submission->alumni?->tahun_lulus,
                    $submission->alumni?->nomor_telepon,
                    optional($submission->submitted_at ?? $submission->updated_at)->format('Y-m-d H:i:s'),
                    $this->mapSingleValue('a1_status_perkawinan', $submission->a1_status_perkawinan, $singleValueLabels),
                    $submission->a2_negara_tinggal,
                    $provinceNames->get($submission->a3_provinsi_id),
                    $cityNames->get($submission->a4_kota_id),
                    $submission->a5_email_aktif,
                    $submission->a6_no_hp,
                    $this->mapBoolean($submission->b1_studi_lanjut),
                    $this->mapBoolean($submission->b2_bekerja),
                    $this->mapSingleValue('b3_bentuk_pekerjaan', $submission->b3_bentuk_pekerjaan, $singleValueLabels),
                    $this->mapBoolean($submission->b4_penghasilan_min_1jam),
                    $this->mapBoolean($submission->b5_membantu_usaha),
                    $this->mapBoolean($submission->b6_sementara_tidak_bekerja),
                    $this->mapSingleValue('c1_waktu_pekerjaan_pertama', $submission->c1_waktu_pekerjaan_pertama, $singleValueLabels),
                    $this->mapSingleValue('c2_lokasi_kerja', $submission->c2_lokasi_kerja, $singleValueLabels),
                    $submission->c3_jabatan,
                    $submission->c4_nama_perusahaan,
                    $submission->c5_nama_atasan,
                    $submission->c6_jabatan_atasan,
                    $submission->c7_kontak_atasan,
                    $this->mapSingleValue('c8_jenis_instansi', $submission->c8_jenis_instansi, $singleValueLabels),
                    $submission->c9_jam_kerja_per_minggu,
                    $this->mapSingleValue('c10_penghasilan_bulanan', $submission->c10_penghasilan_bulanan, $singleValueLabels),
                    $this->mapSingleValue('c11_frekuensi_ganti_kerja', $submission->c11_frekuensi_ganti_kerja, $singleValueLabels),
                    $this->mapSingleValue('c12_alasan_ganti_kerja', $submission->c12_alasan_ganti_kerja, $singleValueLabels),
                    $submission->c12_alasan_lainnya,
                    $this->mapMultiValue('c13_cara_dapat_kerja', $submission->c13_cara_dapat_kerja, $multiValueLabels),
                    $submission->c13_cara_lainnya,
                    $this->mapSingleValue('c14_kesesuaian_pekerjaan', $submission->c14_kesesuaian_pekerjaan, $singleValueLabels),
                    $this->mapSingleValue('d1_lokasi_studi', $submission->d1_lokasi_studi, $singleValueLabels),
                    $this->mapSingleValue('d2_jenjang', $submission->d2_jenjang, $singleValueLabels),
                    $submission->d3_nama_pt,
                    $submission->d4_program_studi,
                    $this->mapSingleValue('d5_kesesuaian_studi', $submission->d5_kesesuaian_studi, $singleValueLabels),
                    optional($submission->d6_mulai_studi)->format('Y-m-d'),
                    $this->mapMultiValue('d7_alasan_lanjut', $submission->d7_alasan_lanjut, $multiValueLabels),
                    $submission->d7_alasan_lainnya,
                    $this->mapMultiValue('e1_aktivitas_mingguan', $submission->e1_aktivitas_mingguan, $multiValueLabels),
                    $this->mapMultiValue('e2_aktivitas_cari_kerja', $submission->e2_aktivitas_cari_kerja, $multiValueLabels),
                    $submission->e3_lama_cari_bulan,
                    $this->mapMultiValue('e4_alasan_mencari', $submission->e4_alasan_mencari, $multiValueLabels),
                    $this->mapSingleValue('f1_lokasi_usaha', $submission->f1_lokasi_usaha, $singleValueLabels),
                    $this->mapSingleValue('f2_bentuk_usaha', $submission->f2_bentuk_usaha, $singleValueLabels),
                    $submission->f2_bentuk_usaha_lainnya,
                    $submission->f3_bidang_usaha,
                    $this->mapSingleValue('f4_produk_usaha', $submission->f4_produk_usaha, $singleValueLabels),
                    $this->mapSingleValue('f5_kepemilikan', $submission->f5_kepemilikan, $singleValueLabels),
                    optional($submission->f6_mulai_usaha)->format('Y-m-d'),
                    $this->mapSingleValue('f7_omset_bulanan', $submission->f7_omset_bulanan, $singleValueLabels),
                    $this->mapSingleValue('f8_riwayat_ganti_usaha', $submission->f8_riwayat_ganti_usaha, $singleValueLabels),
                    $this->mapMultiValue('g1_alasan_pilih_smk', $submission->g1_alasan_pilih_smk, $multiValueLabels),
                    $submission->g1_alasan_lainnya,
                    $this->mapSingleValue('g2_durasi_pkl', $submission->g2_durasi_pkl, $singleValueLabels),
                    $this->mapSingleValue('g3_kualitas_pkl', $submission->g3_kualitas_pkl, $singleValueLabels),
                    $this->mapSingleValue('g4_kesesuaian_pkl', $submission->g4_kesesuaian_pkl, $singleValueLabels),
                ];
            });
    }

    private function mapBoolean(?bool $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value ? 'Ya' : 'Tidak';
    }

    private function mapSingleValue(string $field, mixed $value, array $labels): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $labels[$field][$value] ?? $value;
    }

    private function mapMultiValue(string $field, mixed $values, array $labels): ?string
    {
        if (! is_array($values) || count($values) === 0) {
            return null;
        }

        $mapped = collect($values)
            ->map(fn ($value) => $labels[$field][$value] ?? $value)
            ->all();

        return implode(', ', $mapped);
    }

    private function singleValueLabels(): array
    {
        return [
            'a1_status_perkawinan' => ['belum_menikah' => 'Belum menikah', 'sudah_menikah' => 'Sudah menikah', 'cerai' => 'Cerai'],
            'b3_bentuk_pekerjaan' => [
                'wirausaha_tanpa_pekerja' => 'Wirausaha tanpa pekerja',
                'wirausaha_pekerja_tidak_dibayar' => 'Wirausaha pekerja tidak dibayar',
                'wirausaha_pekerja_dibayar' => 'Wirausaha pekerja dibayar',
                'membantu_usaha_keluarga' => 'Membantu usaha keluarga',
                'buruh_karyawan_pegawai' => 'Buruh / Karyawan / Pegawai',
                'pekerja_bebas' => 'Pekerja bebas',
            ],
            'c1_waktu_pekerjaan_pertama' => ['sebelum_lulus' => 'Sebelum lulus', 'setelah_lulus' => 'Setelah lulus'],
            'c2_lokasi_kerja' => ['dalam_negeri' => 'Dalam negeri', 'luar_negeri' => 'Luar negeri'],
            'c8_jenis_instansi' => [
                'instansi_pemerintah' => 'Instansi pemerintah',
                'lembaga_internasional' => 'Lembaga internasional',
                'lembaga_non_profit' => 'Lembaga non-profit',
                'perusahaan_swasta_bumn_bumd' => 'Perusahaan swasta / BUMN / BUMD',
                'koperasi' => 'Koperasi',
                'usaha_perorangan' => 'Usaha perorangan',
                'rumah_tangga' => 'Rumah tangga',
            ],
            'c10_penghasilan_bulanan' => ['kurang_3_juta' => '< 3 juta', '3_5_juta' => '3 - 5 juta', 'lebih_5_juta' => '> 5 juta'],
            'c11_frekuensi_ganti_kerja' => ['belum_pernah' => 'Belum pernah', 'satu_kali' => 'Satu kali', 'dua_kali' => 'Dua kali', 'tiga_atau_lebih' => 'Tiga kali atau lebih'],
            'c12_alasan_ganti_kerja' => [
                'phk' => 'Di-PHK',
                'gaji_kurang' => 'Gaji kurang',
                'beban_berat' => 'Beban terlalu berat',
                'kurang_menantang' => 'Kurang menantang',
                'karir_sulit' => 'Karir sulit berkembang',
                'iklim_kerja' => 'Iklim kerja kurang kondusif',
                'kontrak_selesai' => 'Kontrak selesai',
                'lainnya' => 'Lainnya',
            ],
            'c14_kesesuaian_pekerjaan' => [
                'sangat_tidak_selaras' => 'Sangat tidak selaras',
                'tidak_selaras' => 'Tidak selaras',
                'selaras' => 'Selaras',
                'sangat_selaras' => 'Sangat selaras',
            ],
            'd1_lokasi_studi' => ['dalam_negeri' => 'Dalam negeri', 'luar_negeri' => 'Luar negeri'],
            'd2_jenjang' => ['d1' => 'D1', 'd2' => 'D2', 'd3' => 'D3', 'd4' => 'D4 / Sarjana Terapan', 's1' => 'S1'],
            'd5_kesesuaian_studi' => [
                'sangat_tidak_selaras' => 'Sangat tidak selaras',
                'tidak_selaras' => 'Tidak selaras',
                'selaras' => 'Selaras',
                'sangat_selaras' => 'Sangat selaras',
            ],
            'f1_lokasi_usaha' => ['dalam_negeri' => 'Dalam negeri', 'luar_negeri' => 'Luar negeri'],
            'f2_bentuk_usaha' => ['perorangan' => 'Usaha perorangan', 'koperasi' => 'Koperasi', 'firma' => 'Firma', 'cv' => 'CV', 'pt' => 'PT', 'lainnya' => 'Lainnya'],
            'f4_produk_usaha' => ['barang' => 'Barang', 'jasa' => 'Jasa', 'barang_jasa' => 'Barang dan jasa'],
            'f5_kepemilikan' => ['milik_sendiri' => 'Milik sendiri', 'milik_bersama' => 'Milik bersama'],
            'f7_omset_bulanan' => ['kurang_25_jt' => '< 25 jt', '25_50_jt' => '25 - 50 jt', '50_100_jt' => '50 - 100 jt', 'lebih_100_jt' => '> 100 jt'],
            'f8_riwayat_ganti_usaha' => ['belum_pernah' => 'Belum pernah', '1x' => '1x', '2x' => '2x', '3x_atau_lebih' => '3x atau lebih'],
            'g2_durasi_pkl' => ['kurang_6_bulan' => '< 6 bulan', '6_bulan' => '6 bulan', 'lebih_6_bulan' => '> 6 bulan'],
            'g3_kualitas_pkl' => ['sangat_tidak_baik' => 'Sangat tidak baik', 'tidak_baik' => 'Tidak baik', 'baik' => 'Baik', 'sangat_baik' => 'Sangat baik'],
            'g4_kesesuaian_pkl' => ['sangat_tidak_baik' => 'Sangat tidak baik', 'tidak_baik' => 'Tidak baik', 'baik' => 'Baik', 'sangat_baik' => 'Sangat baik'],
        ];
    }

    private function multiValueLabels(): array
    {
        return [
            'c13_cara_dapat_kerja' => [
                'mitra_smk' => 'Melalui industri mitra SMK',
                'bkk_smk' => 'Melalui bursa kerja SMK',
                'tempat_magang' => 'Melalui tempat PKL',
                'ikatan_alumni' => 'Melalui ikatan alumni',
                'iklan' => 'Melalui iklan media',
                'job_fair' => 'Melalui job fair',
                'dinas_tk' => 'Melalui dinas ketenagakerjaan',
                'bantuan_orang_lain' => 'Bantuan orang lain/keluarga',
                'lainnya' => 'Lainnya',
            ],
            'd7_alasan_lanjut' => [
                'meningkatkan_kompetensi' => 'Meningkatkan kompetensi',
                'status_sosial' => 'Meningkatkan status sosial',
                'beasiswa' => 'Memperoleh beasiswa',
                'saran_orangtua' => 'Saran orang tua/keluarga',
                'belum_dapat_kerja' => 'Belum menemukan pekerjaan tepat',
                'lainnya' => 'Lainnya',
            ],
            'e1_aktivitas_mingguan' => [
                'rumah_tangga' => 'Mengurus rumah tangga',
                'pelatihan' => 'Mengikuti pelatihan/kursus',
                'persiapan_studi' => 'Mempersiapkan studi',
                'organisasi_sosial' => 'Terlibat organisasi sosial',
                'tidak_termasuk' => 'Tidak termasuk semua pilihan di atas',
            ],
            'e2_aktivitas_cari_kerja' => [
                'kirim_lamaran' => 'Mempersiapkan/mengirim lamaran',
                'ikut_seleksi' => 'Mengikuti seleksi kerja',
                'menunggu_hasil' => 'Menunggu hasil lamaran',
                'modal_usaha' => 'Mengumpulkan modal usaha',
                'lokasi_usaha' => 'Mencari lokasi usaha',
                'izin_usaha' => 'Mengurus izin usaha',
                'tidak_melakukan' => 'Tidak melakukan semua kegiatan di atas',
            ],
            'e4_alasan_mencari' => [
                'tidak_sesuai_keahlian' => 'Tidak sesuai keahlian',
                'tidak_lanjut_kuliah' => 'Tidak lanjut kuliah',
                'upah_kurang' => 'Upah kurang layak',
                'phk' => 'PHK',
                'usaha_bangkrut' => 'Usaha bangkrut',
                'kontrak_habis' => 'Masa kontrak habis',
            ],
            'g1_alasan_pilih_smk' => [
                'cepat_kerja' => 'Ingin cepat dapat pekerjaan',
                'keinginan_sendiri' => 'Keinginan sendiri',
                'diajak_teman' => 'Diajak teman',
                'keinginan_orangtua' => 'Keinginan orang tua/keluarga',
                'tidak_diterima_lain' => 'Tidak diterima di sekolah lain',
                'lainnya' => 'Lainnya',
            ],
        ];
    }
}
