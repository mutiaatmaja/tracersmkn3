<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TracerSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\Facades\Pdf;

class LaporanController extends Controller
{
    public function alumniPdf(Request $request)
    {
        $tahunLulusFilter = $request->string('tahun_lulus')->toString();

        $query = Alumni::query();

        if ($tahunLulusFilter !== '') {
            $query->where('tahun_lulus', (int) $tahunLulusFilter);
        }

        $alumniPerTahun = (clone $query)
            ->selectRaw('tahun_lulus, COUNT(*) as total')
            ->whereNotNull('tahun_lulus')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        $totalAlumni = (clone $query)->count();
        $sudahKlaim = (clone $query)->where('is_claimed', true)->count();
        $belumKlaim = $totalAlumni - $sudahKlaim;

        $statistikJenisKelamin = (clone $query)
            ->selectRaw('jenis_kelamin, COUNT(*) as total')
            ->whereNotNull('jenis_kelamin')
            ->groupBy('jenis_kelamin')
            ->orderBy('jenis_kelamin')
            ->get();

        $alumniDenganTanggalLahir = (clone $query)
            ->whereNotNull('tanggal_lahir')
            ->get(['tanggal_lahir']);

        $usiaList = $alumniDenganTanggalLahir
            ->map(fn ($alumni) => now()->diffInYears($alumni->tanggal_lahir))
            ->values();

        $bucketUsia = [
            '< 20 tahun' => 0,
            '20 - 24 tahun' => 0,
            '25 - 29 tahun' => 0,
            '>= 30 tahun' => 0,
        ];

        foreach ($usiaList as $usia) {
            if ($usia < 20) {
                $bucketUsia['< 20 tahun']++;

                continue;
            }

            if ($usia <= 24) {
                $bucketUsia['20 - 24 tahun']++;

                continue;
            }

            if ($usia <= 29) {
                $bucketUsia['25 - 29 tahun']++;

                continue;
            }

            $bucketUsia['>= 30 tahun']++;
        }

        $statistikUmur = [
            'rata_rata' => $usiaList->isNotEmpty() ? round($usiaList->avg(), 1) : 0,
            'total_terdata' => $usiaList->count(),
            'total_tidak_terdata' => (clone $query)->whereNull('tanggal_lahir')->count(),
            'bucket' => $bucketUsia,
        ];

        return Pdf::view('reports.alumni-statistics', [
            'alumniPerTahun' => $alumniPerTahun,
            'totalAlumni' => $totalAlumni,
            'sudahKlaim' => $sudahKlaim,
            'belumKlaim' => $belumKlaim,
            'statistikJenisKelamin' => $statistikJenisKelamin,
            'statistikUmur' => $statistikUmur,
            'tahunLulusFilter' => $tahunLulusFilter !== '' ? (int) $tahunLulusFilter : null,
        ])
            ->format('a4')
            ->name('laporan-statistik-alumni.pdf')
            ->download();
    }

    public function tracerPdf(Request $request)
    {
        $periodeFilter = $request->string('periode_tahun')->toString();

        $query = TracerSubmission::query();

        if ($periodeFilter !== '') {
            $query->where('periode_tahun', (int) $periodeFilter);
        }

        $tracerPerPeriode = (clone $query)
            ->selectRaw('periode_tahun, COUNT(*) as total')
            ->groupBy('periode_tahun')
            ->orderByDesc('periode_tahun')
            ->get();

        $totalRespon = (clone $query)->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $draft = (clone $query)->where('status', 'draft')->count();
        $persenSubmitted = $totalRespon > 0 ? round(($submitted / $totalRespon) * 100, 1) : 0;

        $statusKegiatan = [
            'studi_lanjut' => (clone $query)->where('b1_studi_lanjut', true)->count(),
            'bekerja' => (clone $query)->where('b2_bekerja', true)->count(),
            'belum_keduanya' => (clone $query)->where('b1_studi_lanjut', false)->where('b2_bekerja', false)->count(),
        ];

        $topNegara = (clone $query)
            ->selectRaw('a2_negara_tinggal, COUNT(*) as total')
            ->whereNotNull('a2_negara_tinggal')
            ->where('a2_negara_tinggal', '!=', '')
            ->groupBy('a2_negara_tinggal')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $questionStats = $this->buildTracerQuestionStatistics((clone $query)->get());

        return Pdf::view('reports.tracer-statistics', [
            'tracerPerPeriode' => $tracerPerPeriode,
            'totalRespon' => $totalRespon,
            'submitted' => $submitted,
            'draft' => $draft,
            'persenSubmitted' => $persenSubmitted,
            'statusKegiatan' => $statusKegiatan,
            'topNegara' => $topNegara,
            'questionStats' => $questionStats,
            'periodeFilter' => $periodeFilter !== '' ? (int) $periodeFilter : null,
        ])
            ->format('a4')
            ->name('laporan-statistik-tracer-study.pdf')
            ->download();
    }

    private function tracerSingleChoiceDefinitions(): array
    {
        return [
            ['field' => 'a1_status_perkawinan', 'label' => 'A1. Status Perkawinan', 'options' => [['value' => 'belum_menikah', 'label' => 'Belum menikah'], ['value' => 'sudah_menikah', 'label' => 'Sudah menikah'], ['value' => 'cerai', 'label' => 'Cerai']]],
            ['field' => 'b1_studi_lanjut', 'label' => 'B1. Studi Lanjut', 'options' => [['value' => true, 'label' => 'Ya'], ['value' => false, 'label' => 'Tidak']]],
            ['field' => 'b2_bekerja', 'label' => 'B2. Bekerja / Berwirausaha', 'options' => [['value' => true, 'label' => 'Ya'], ['value' => false, 'label' => 'Tidak']]],
            ['field' => 'b3_bentuk_pekerjaan', 'label' => 'B3. Bentuk Pekerjaan', 'options' => [['value' => 'wirausaha_tanpa_pekerja', 'label' => 'Wirausaha tanpa pekerja'], ['value' => 'wirausaha_pekerja_tidak_dibayar', 'label' => 'Wirausaha pekerja tidak dibayar'], ['value' => 'wirausaha_pekerja_dibayar', 'label' => 'Wirausaha pekerja dibayar'], ['value' => 'membantu_usaha_keluarga', 'label' => 'Membantu usaha keluarga'], ['value' => 'buruh_karyawan_pegawai', 'label' => 'Buruh / Karyawan / Pegawai'], ['value' => 'pekerja_bebas', 'label' => 'Pekerja bebas']]],
            ['field' => 'b4_penghasilan_min_1jam', 'label' => 'B4. Minimal 1 jam untuk penghasilan', 'options' => [['value' => true, 'label' => 'Ya'], ['value' => false, 'label' => 'Tidak']]],
            ['field' => 'b5_membantu_usaha', 'label' => 'B5. Membantu usaha keluarga', 'options' => [['value' => true, 'label' => 'Ya'], ['value' => false, 'label' => 'Tidak']]],
            ['field' => 'b6_sementara_tidak_bekerja', 'label' => 'B6. Punya usaha tapi sementara tidak bekerja', 'options' => [['value' => true, 'label' => 'Ya'], ['value' => false, 'label' => 'Tidak']]],
            ['field' => 'c1_waktu_pekerjaan_pertama', 'label' => 'C1. Waktu Mendapat Pekerjaan', 'options' => [['value' => 'sebelum_lulus', 'label' => 'Sebelum lulus'], ['value' => 'setelah_lulus', 'label' => 'Setelah lulus']]],
            ['field' => 'c2_lokasi_kerja', 'label' => 'C2. Lokasi Tempat Kerja', 'options' => [['value' => 'dalam_negeri', 'label' => 'Dalam negeri'], ['value' => 'luar_negeri', 'label' => 'Luar negeri']]],
            ['field' => 'c8_jenis_instansi', 'label' => 'C8. Jenis Instansi', 'options' => [['value' => 'instansi_pemerintah', 'label' => 'Instansi pemerintah'], ['value' => 'lembaga_internasional', 'label' => 'Lembaga internasional'], ['value' => 'lembaga_non_profit', 'label' => 'Lembaga non-profit'], ['value' => 'perusahaan_swasta_bumn_bumd', 'label' => 'Perusahaan swasta / BUMN / BUMD'], ['value' => 'koperasi', 'label' => 'Koperasi'], ['value' => 'usaha_perorangan', 'label' => 'Usaha perorangan'], ['value' => 'rumah_tangga', 'label' => 'Rumah tangga']]],
            ['field' => 'c10_penghasilan_bulanan', 'label' => 'C10. Penghasilan Bulanan', 'options' => [['value' => 'kurang_3_juta', 'label' => '< 3 juta'], ['value' => '3_5_juta', 'label' => '3 - 5 juta'], ['value' => 'lebih_5_juta', 'label' => '> 5 juta']]],
            ['field' => 'c11_frekuensi_ganti_kerja', 'label' => 'C11. Frekuensi Ganti Pekerjaan', 'options' => [['value' => 'belum_pernah', 'label' => 'Belum pernah'], ['value' => 'satu_kali', 'label' => 'Satu kali'], ['value' => 'dua_kali', 'label' => 'Dua kali'], ['value' => 'tiga_atau_lebih', 'label' => 'Tiga kali atau lebih']]],
            ['field' => 'c12_alasan_ganti_kerja', 'label' => 'C12. Alasan Ganti Pekerjaan', 'options' => [['value' => 'phk', 'label' => 'Di-PHK'], ['value' => 'gaji_kurang', 'label' => 'Gaji kurang'], ['value' => 'beban_berat', 'label' => 'Beban terlalu berat'], ['value' => 'kurang_menantang', 'label' => 'Kurang menantang'], ['value' => 'karir_sulit', 'label' => 'Karir sulit berkembang'], ['value' => 'iklim_kerja', 'label' => 'Iklim kerja kurang kondusif'], ['value' => 'kontrak_selesai', 'label' => 'Kontrak selesai'], ['value' => 'lainnya', 'label' => 'Lainnya']]],
            ['field' => 'c14_kesesuaian_pekerjaan', 'label' => 'C14. Kesesuaian Pekerjaan', 'options' => [['value' => 'sangat_tidak_selaras', 'label' => 'Sangat tidak selaras'], ['value' => 'tidak_selaras', 'label' => 'Tidak selaras'], ['value' => 'selaras', 'label' => 'Selaras'], ['value' => 'sangat_selaras', 'label' => 'Sangat selaras']]],
            ['field' => 'd1_lokasi_studi', 'label' => 'D1. Lokasi Studi', 'options' => [['value' => 'dalam_negeri', 'label' => 'Dalam negeri'], ['value' => 'luar_negeri', 'label' => 'Luar negeri']]],
            ['field' => 'd2_jenjang', 'label' => 'D2. Jenjang Pendidikan', 'options' => [['value' => 'd1', 'label' => 'D1'], ['value' => 'd2', 'label' => 'D2'], ['value' => 'd3', 'label' => 'D3'], ['value' => 'd4', 'label' => 'D4 / Sarjana Terapan'], ['value' => 's1', 'label' => 'S1']]],
            ['field' => 'd5_kesesuaian_studi', 'label' => 'D5. Kesesuaian Studi', 'options' => [['value' => 'sangat_tidak_selaras', 'label' => 'Sangat tidak selaras'], ['value' => 'tidak_selaras', 'label' => 'Tidak selaras'], ['value' => 'selaras', 'label' => 'Selaras'], ['value' => 'sangat_selaras', 'label' => 'Sangat selaras']]],
            ['field' => 'f1_lokasi_usaha', 'label' => 'F1. Lokasi Usaha', 'options' => [['value' => 'dalam_negeri', 'label' => 'Dalam negeri'], ['value' => 'luar_negeri', 'label' => 'Luar negeri']]],
            ['field' => 'f2_bentuk_usaha', 'label' => 'F2. Bentuk Usaha', 'options' => [['value' => 'perorangan', 'label' => 'Usaha perorangan'], ['value' => 'koperasi', 'label' => 'Koperasi'], ['value' => 'firma', 'label' => 'Firma'], ['value' => 'cv', 'label' => 'CV'], ['value' => 'pt', 'label' => 'PT'], ['value' => 'lainnya', 'label' => 'Lainnya']]],
            ['field' => 'f4_produk_usaha', 'label' => 'F4. Produk Usaha', 'options' => [['value' => 'barang', 'label' => 'Barang'], ['value' => 'jasa', 'label' => 'Jasa'], ['value' => 'barang_jasa', 'label' => 'Barang dan jasa']]],
            ['field' => 'f5_kepemilikan', 'label' => 'F5. Kepemilikan Usaha', 'options' => [['value' => 'milik_sendiri', 'label' => 'Milik sendiri'], ['value' => 'milik_bersama', 'label' => 'Milik bersama']]],
            ['field' => 'f7_omset_bulanan', 'label' => 'F7. Omset Bulanan', 'options' => [['value' => 'kurang_25_jt', 'label' => '< 25 jt'], ['value' => '25_50_jt', 'label' => '25 - 50 jt'], ['value' => '50_100_jt', 'label' => '50 - 100 jt'], ['value' => 'lebih_100_jt', 'label' => '> 100 jt']]],
            ['field' => 'f8_riwayat_ganti_usaha', 'label' => 'F8. Riwayat Pergantian Usaha', 'options' => [['value' => 'belum_pernah', 'label' => 'Belum pernah'], ['value' => '1x', 'label' => '1x'], ['value' => '2x', 'label' => '2x'], ['value' => '3x_atau_lebih', 'label' => '3x atau lebih']]],
            ['field' => 'g2_durasi_pkl', 'label' => 'G2. Durasi PKL', 'options' => [['value' => 'kurang_6_bulan', 'label' => '< 6 bulan'], ['value' => '6_bulan', 'label' => '6 bulan'], ['value' => 'lebih_6_bulan', 'label' => '> 6 bulan']]],
            ['field' => 'g3_kualitas_pkl', 'label' => 'G3. Kualitas PKL', 'options' => [['value' => 'sangat_tidak_baik', 'label' => 'Sangat tidak baik'], ['value' => 'tidak_baik', 'label' => 'Tidak baik'], ['value' => 'baik', 'label' => 'Baik'], ['value' => 'sangat_baik', 'label' => 'Sangat baik']]],
            ['field' => 'g4_kesesuaian_pkl', 'label' => 'G4. Kesesuaian PKL', 'options' => [['value' => 'sangat_tidak_baik', 'label' => 'Sangat tidak baik'], ['value' => 'tidak_baik', 'label' => 'Tidak baik'], ['value' => 'baik', 'label' => 'Baik'], ['value' => 'sangat_baik', 'label' => 'Sangat baik']]],
        ];
    }

    private function tracerMultiChoiceDefinitions(): array
    {
        return [
            ['field' => 'c13_cara_dapat_kerja', 'label' => 'C13. Cara Mendapatkan Pekerjaan Pertama', 'options' => [['value' => 'mitra_smk', 'label' => 'Melalui industri mitra SMK'], ['value' => 'bkk_smk', 'label' => 'Melalui bursa kerja SMK'], ['value' => 'tempat_magang', 'label' => 'Melalui tempat PKL'], ['value' => 'ikatan_alumni', 'label' => 'Melalui ikatan alumni'], ['value' => 'iklan', 'label' => 'Melalui iklan media'], ['value' => 'job_fair', 'label' => 'Melalui job fair'], ['value' => 'dinas_tk', 'label' => 'Melalui dinas ketenagakerjaan'], ['value' => 'bantuan_orang_lain', 'label' => 'Bantuan orang lain/keluarga'], ['value' => 'lainnya', 'label' => 'Lainnya']]],
            ['field' => 'd7_alasan_lanjut', 'label' => 'D7. Alasan Melanjutkan Studi', 'options' => [['value' => 'meningkatkan_kompetensi', 'label' => 'Meningkatkan kompetensi'], ['value' => 'status_sosial', 'label' => 'Meningkatkan status sosial'], ['value' => 'beasiswa', 'label' => 'Memperoleh beasiswa'], ['value' => 'saran_orangtua', 'label' => 'Saran orang tua/keluarga'], ['value' => 'belum_dapat_kerja', 'label' => 'Belum menemukan pekerjaan tepat'], ['value' => 'lainnya', 'label' => 'Lainnya']]],
            ['field' => 'e1_aktivitas_mingguan', 'label' => 'E1. Aktivitas Mingguan', 'options' => [['value' => 'rumah_tangga', 'label' => 'Mengurus rumah tangga'], ['value' => 'pelatihan', 'label' => 'Mengikuti pelatihan/kursus'], ['value' => 'persiapan_studi', 'label' => 'Mempersiapkan studi'], ['value' => 'organisasi_sosial', 'label' => 'Terlibat organisasi sosial'], ['value' => 'tidak_termasuk', 'label' => 'Tidak termasuk semua pilihan di atas']]],
            ['field' => 'e2_aktivitas_cari_kerja', 'label' => 'E2. Aktivitas Mencari Kerja', 'options' => [['value' => 'kirim_lamaran', 'label' => 'Mempersiapkan/mengirim lamaran'], ['value' => 'ikut_seleksi', 'label' => 'Mengikuti seleksi kerja'], ['value' => 'menunggu_hasil', 'label' => 'Menunggu hasil lamaran'], ['value' => 'modal_usaha', 'label' => 'Mengumpulkan modal usaha'], ['value' => 'lokasi_usaha', 'label' => 'Mencari lokasi usaha'], ['value' => 'izin_usaha', 'label' => 'Mengurus izin usaha'], ['value' => 'tidak_melakukan', 'label' => 'Tidak melakukan semua kegiatan di atas']]],
            ['field' => 'e4_alasan_mencari', 'label' => 'E4. Alasan Mencari Pekerjaan', 'options' => [['value' => 'tidak_sesuai_keahlian', 'label' => 'Tidak sesuai keahlian'], ['value' => 'tidak_lanjut_kuliah', 'label' => 'Tidak lanjut kuliah'], ['value' => 'upah_kurang', 'label' => 'Upah kurang layak'], ['value' => 'phk', 'label' => 'PHK'], ['value' => 'usaha_bangkrut', 'label' => 'Usaha bangkrut'], ['value' => 'kontrak_habis', 'label' => 'Masa kontrak habis']]],
            ['field' => 'g1_alasan_pilih_smk', 'label' => 'G1. Alasan Memilih SMK', 'options' => [['value' => 'cepat_kerja', 'label' => 'Ingin cepat dapat pekerjaan'], ['value' => 'keinginan_sendiri', 'label' => 'Keinginan sendiri'], ['value' => 'diajak_teman', 'label' => 'Diajak teman'], ['value' => 'keinginan_orangtua', 'label' => 'Keinginan orang tua/keluarga'], ['value' => 'tidak_diterima_lain', 'label' => 'Tidak diterima di sekolah lain'], ['value' => 'lainnya', 'label' => 'Lainnya']]],
        ];
    }

    private function buildTracerQuestionStatistics(Collection $rows): array
    {
        $statistics = [];

        foreach ($this->tracerSingleChoiceDefinitions() as $definition) {
            $field = $definition['field'];
            $answeredRows = $rows->filter(fn ($row) => ! blank($row->{$field}));
            $answeredCount = $answeredRows->count();

            $options = collect($definition['options'])->map(function (array $option) use ($answeredRows, $field, $answeredCount): array {
                $count = $answeredRows->filter(fn ($row) => $row->{$field} === $option['value'])->count();

                return [
                    'label' => $option['label'],
                    'count' => $count,
                    'percent' => $answeredCount > 0 ? round(($count / $answeredCount) * 100, 1) : 0,
                ];
            })->all();

            $statistics[] = [
                'label' => $definition['label'],
                'answered' => $answeredCount,
                'options' => $options,
            ];
        }

        foreach ($this->tracerMultiChoiceDefinitions() as $definition) {
            $field = $definition['field'];
            $answeredRows = $rows->filter(fn ($row) => is_array($row->{$field}) && count($row->{$field}) > 0);
            $answeredCount = $answeredRows->count();

            $options = collect($definition['options'])->map(function (array $option) use ($answeredRows, $field, $answeredCount): array {
                $count = $answeredRows->filter(
                    fn ($row) => is_array($row->{$field}) && in_array($option['value'], $row->{$field}, true)
                )->count();

                return [
                    'label' => $option['label'],
                    'count' => $count,
                    'percent' => $answeredCount > 0 ? round(($count / $answeredCount) * 100, 1) : 0,
                ];
            })->all();

            $statistics[] = [
                'label' => $definition['label'].' (multi jawaban)',
                'answered' => $answeredCount,
                'options' => $options,
            ];
        }

        return $statistics;
    }
}
