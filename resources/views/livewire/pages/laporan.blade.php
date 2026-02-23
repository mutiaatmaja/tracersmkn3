<?php

use App\Models\Alumni;
use App\Models\TracerSubmission;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Livewire Component untuk halaman Laporan
 */
new class extends Component {
    public string $jenisLaporan = 'alumni';

    public string $filterTahunLulus = '';

    public string $filterPeriodeTracer = '';

    protected function filteredAlumniQuery(): Builder
    {
        $query = Alumni::query();

        if ($this->filterTahunLulus !== '') {
            $query->where('tahun_lulus', (int) $this->filterTahunLulus);
        }

        return $query;
    }

    protected function filteredTracerQuery(): Builder
    {
        $query = TracerSubmission::query();

        if ($this->filterPeriodeTracer !== '') {
            $query->where('periode_tahun', (int) $this->filterPeriodeTracer);
        }

        return $query;
    }

    #[Computed]
    public function daftarTahunLulus()
    {
        return Alumni::query()->whereNotNull('tahun_lulus')->select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');
    }

    #[Computed]
    public function daftarPeriodeTracer()
    {
        return TracerSubmission::query()->select('periode_tahun')->distinct()->orderByDesc('periode_tahun')->pluck('periode_tahun');
    }

    #[Computed]
    public function alumniPerTahun()
    {
        return $this->filteredAlumniQuery()->selectRaw('tahun_lulus, COUNT(*) as total')->whereNotNull('tahun_lulus')->groupBy('tahun_lulus')->orderByDesc('tahun_lulus')->get();
    }

    #[Computed]
    public function statistikKlaim(): array
    {
        $query = $this->filteredAlumniQuery();
        $totalAlumni = (clone $query)->count();
        $sudahKlaim = (clone $query)->where('is_claimed', true)->count();
        $belumKlaim = $totalAlumni - $sudahKlaim;
        $persenKlaim = $totalAlumni > 0 ? round(($sudahKlaim / $totalAlumni) * 100, 1) : 0;

        return [
            'total' => $totalAlumni,
            'sudah_klaim' => $sudahKlaim,
            'belum_klaim' => $belumKlaim,
            'persen_klaim' => $persenKlaim,
        ];
    }

    #[Computed]
    public function statistikJenisKelamin()
    {
        return $this->filteredAlumniQuery()->selectRaw('jenis_kelamin, COUNT(*) as total')->whereNotNull('jenis_kelamin')->groupBy('jenis_kelamin')->orderBy('jenis_kelamin')->get();
    }

    #[Computed]
    public function statistikUmur(): array
    {
        $query = $this->filteredAlumniQuery();

        $alumniDenganTanggalLahir = (clone $query)->whereNotNull('tanggal_lahir')->get(['tanggal_lahir']);

        $usiaList = $alumniDenganTanggalLahir->map(fn($alumni) => now()->diffInYears($alumni->tanggal_lahir))->values();

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

        return [
            'rata_rata' => $usiaList->isNotEmpty() ? round($usiaList->avg(), 1) : 0,
            'total_terdata' => $usiaList->count(),
            'total_tidak_terdata' => (clone $query)->whereNull('tanggal_lahir')->count(),
            'bucket' => $bucketUsia,
        ];
    }

    #[Computed]
    public function statistikTracerRingkas(): array
    {
        $query = $this->filteredTracerQuery();

        $total = (clone $query)->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $draft = (clone $query)->where('status', 'draft')->count();

        return [
            'total' => $total,
            'submitted' => $submitted,
            'draft' => $draft,
            'persen_submitted' => $total > 0 ? round(($submitted / $total) * 100, 1) : 0,
        ];
    }

    #[Computed]
    public function tracerPerPeriode()
    {
        return $this->filteredTracerQuery()->selectRaw('periode_tahun, COUNT(*) as total')->groupBy('periode_tahun')->orderByDesc('periode_tahun')->get();
    }

    #[Computed]
    public function tracerStatusKegiatan(): array
    {
        $query = $this->filteredTracerQuery();

        $studiLanjut = (clone $query)->where('b1_studi_lanjut', true)->count();
        $bekerja = (clone $query)->where('b2_bekerja', true)->count();
        $belumKeduanya = (clone $query)->where('b1_studi_lanjut', false)->where('b2_bekerja', false)->count();

        return [
            'studi_lanjut' => $studiLanjut,
            'bekerja' => $bekerja,
            'belum_keduanya' => $belumKeduanya,
        ];
    }

    #[Computed]
    public function tracerTopNegara()
    {
        return $this->filteredTracerQuery()->selectRaw('a2_negara_tinggal, COUNT(*) as total')->whereNotNull('a2_negara_tinggal')->where('a2_negara_tinggal', '!=', '')->groupBy('a2_negara_tinggal')->orderByDesc('total')->limit(10)->get();
    }

    protected function tracerSingleChoiceDefinitions(): array
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

    protected function tracerMultiChoiceDefinitions(): array
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

    #[Computed]
    public function tracerStatistikPertanyaan(): array
    {
        $singleDefinitions = $this->tracerSingleChoiceDefinitions();
        $multiDefinitions = $this->tracerMultiChoiceDefinitions();

        $fields = collect($singleDefinitions)
            ->pluck('field')
            ->merge(collect($multiDefinitions)->pluck('field'))
            ->unique()
            ->values()
            ->all();

        $rows = $this->filteredTracerQuery()->get($fields);
        $statistics = [];

        foreach ($singleDefinitions as $definition) {
            $field = $definition['field'];
            $answeredRows = $rows->filter(fn($row) => !blank($row->{$field}));
            $answeredCount = $answeredRows->count();

            $options = collect($definition['options'])
                ->map(function (array $option) use ($answeredRows, $field, $answeredCount): array {
                    $count = $answeredRows->filter(fn($row) => $row->{$field} === $option['value'])->count();

                    return [
                        'label' => $option['label'],
                        'count' => $count,
                        'percent' => $answeredCount > 0 ? round(($count / $answeredCount) * 100, 1) : 0,
                    ];
                })
                ->all();

            $statistics[] = [
                'label' => $definition['label'],
                'answered' => $answeredCount,
                'options' => $options,
            ];
        }

        foreach ($multiDefinitions as $definition) {
            $field = $definition['field'];
            $answeredRows = $rows->filter(fn($row) => is_array($row->{$field}) && count($row->{$field}) > 0);
            $answeredCount = $answeredRows->count();

            $options = collect($definition['options'])
                ->map(function (array $option) use ($answeredRows, $field, $answeredCount): array {
                    $count = $answeredRows->filter(fn($row) => is_array($row->{$field}) && in_array($option['value'], $row->{$field}, true))->count();

                    return [
                        'label' => $option['label'],
                        'count' => $count,
                        'percent' => $answeredCount > 0 ? round(($count / $answeredCount) * 100, 1) : 0,
                    ];
                })
                ->all();

            $statistics[] = [
                'label' => $definition['label'] . ' (multi jawaban)',
                'answered' => $answeredCount,
                'options' => $options,
            ];
        }

        return $statistics;
    }

    public function render()
    {
        return view('livewire.pages.laporan');
    }
};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Laporan</h1>
        <p class="text-gray-600 mt-1">Ringkasan dan laporan tracer study alumni</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-4">
        <p class="mb-3 text-sm font-semibold text-gray-700">Jenis Laporan</p>
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="$set('jenisLaporan', 'alumni')"
                class="rounded-lg border px-4 py-2 text-sm font-semibold {{ $jenisLaporan === 'alumni' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                Laporan Alumni
            </button>
            <button type="button" wire:click="$set('jenisLaporan', 'tracer')"
                class="rounded-lg border px-4 py-2 text-sm font-semibold {{ $jenisLaporan === 'tracer' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                Laporan Tracer Study
            </button>
            <button type="button"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-500">
                Laporan Penempatan Kerja
            </button>
        </div>
    </div>

    @if ($jenisLaporan === 'alumni')
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Laporan Alumni</h2>
                    <p class="text-sm text-gray-600">Statistik alumni per tahun lulus dan status klaim akun</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="filterTahunLulus"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tahun Lulus</option>
                        @foreach ($this->daftarTahunLulus as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>

                    <a href="{{ route('laporan.alumni.pdf', ['tahun_lulus' => $filterTahunLulus !== '' ? $filterTahunLulus : null]) }}"
                        target="_blank"
                        class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                        Cetak PDF Statistik
                    </a>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Total Alumni</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->statistikKlaim['total']) }}
                    </p>
                </div>
                <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                    <p class="text-sm text-green-700">Sudah Klaim</p>
                    <p class="mt-2 text-2xl font-bold text-green-800">
                        {{ number_format($this->statistikKlaim['sudah_klaim']) }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm text-amber-700">Belum Klaim</p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">
                        {{ number_format($this->statistikKlaim['belum_klaim']) }}</p>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">Persentase Klaim</p>
                    <p class="mt-2 text-2xl font-bold text-blue-800">{{ $this->statistikKlaim['persen_klaim'] }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">Statistik Tahun Lulus</h3>
                    </div>
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Tahun Lulus</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Jumlah Alumni</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($this->alumniPerTahun as $row)
                                <tr wire:key="laporan-alumni-tahun-{{ $row->tahun_lulus }}">
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $row->tahun_lulus }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada
                                        data
                                        alumni untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">Statistik Jenis Kelamin</h3>
                    </div>
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Jenis Kelamin</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($this->statistikJenisKelamin as $row)
                                <tr wire:key="laporan-alumni-jk-{{ $row->jenis_kelamin }}">
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ str($row->jenis_kelamin)->title() }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada
                                        data
                                        jenis kelamin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">Statistik Umur</h3>
                </div>
                <div class="space-y-4 p-4">
                    <div class="grid grid-cols-1 gap-3">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Rata-rata Usia</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $this->statistikUmur['rata_rata'] }}
                                tahun
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($this->statistikUmur['bucket'] as $label => $jumlah)
                            <div class="rounded-lg border border-gray-200 bg-white p-3"
                                wire:key="bucket-{{ $label }}">
                                <p class="text-xs text-gray-500">{{ $label }}</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ number_format($jumlah) }}
                                    alumni
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        Data umur terisi: <span
                            class="font-semibold">{{ number_format($this->statistikUmur['total_terdata']) }}</span>
                        · Tidak terisi:
                        <span
                            class="font-semibold">{{ number_format($this->statistikUmur['total_tidak_terdata']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($jenisLaporan === 'tracer')
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Laporan Tracer Study</h2>
                    <p class="text-sm text-gray-600">Statistik pengisian tracer study berdasarkan jawaban alumni</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select wire:model.live="filterPeriodeTracer"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 transition focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Periode</option>
                        @foreach ($this->daftarPeriodeTracer as $periode)
                            <option value="{{ $periode }}">{{ $periode }}</option>
                        @endforeach
                    </select>

                    <a href="{{ route('laporan.tracer.pdf', ['periode_tahun' => $filterPeriodeTracer !== '' ? $filterPeriodeTracer : null]) }}"
                        target="_blank"
                        class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                        Cetak PDF Statistik
                    </a>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm text-gray-500">Total Respon</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ number_format($this->statistikTracerRingkas['total']) }}</p>
                </div>
                <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                    <p class="text-sm text-green-700">Submitted</p>
                    <p class="mt-2 text-2xl font-bold text-green-800">
                        {{ number_format($this->statistikTracerRingkas['submitted']) }}</p>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm text-amber-700">Draft</p>
                    <p class="mt-2 text-2xl font-bold text-amber-800">
                        {{ number_format($this->statistikTracerRingkas['draft']) }}</p>
                </div>
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm text-blue-700">Persentase Submitted</p>
                    <p class="mt-2 text-2xl font-bold text-blue-800">
                        {{ $this->statistikTracerRingkas['persen_submitted'] }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">Respon per Periode</h3>
                    </div>
                    <table class="w-full">
                        <thead class="border-b border-gray-200 bg-gray-50/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Periode</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Jumlah Respon</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($this->tracerPerPeriode as $row)
                                <tr wire:key="laporan-tracer-periode-{{ $row->periode_tahun }}">
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $row->periode_tahun }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($row->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada
                                        data
                                        tracer study.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">Status Kegiatan Alumni (B1/B2)</h3>
                    </div>
                    <div class="space-y-3 p-4">
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3">
                            <p class="text-xs text-blue-700">Melanjutkan Studi</p>
                            <p class="mt-1 text-lg font-bold text-blue-800">
                                {{ number_format($this->tracerStatusKegiatan['studi_lanjut']) }}</p>
                        </div>
                        <div class="rounded-lg border border-green-200 bg-green-50 p-3">
                            <p class="text-xs text-green-700">Bekerja / Berwirausaha</p>
                            <p class="mt-1 text-lg font-bold text-green-800">
                                {{ number_format($this->tracerStatusKegiatan['bekerja']) }}</p>
                        </div>
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                            <p class="text-xs text-amber-700">Belum Studi & Belum Bekerja</p>
                            <p class="mt-1 text-lg font-bold text-amber-800">
                                {{ number_format($this->tracerStatusKegiatan['belum_keduanya']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">Top Negara Tinggal Alumni</h3>
                </div>
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Negara</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($this->tracerTopNegara as $row)
                            <tr wire:key="laporan-tracer-negara-{{ str($row->a2_negara_tinggal)->slug() }}">
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $row->a2_negara_tinggal }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
                                    negara tracer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-4">
                <h3 class="mb-3 text-sm font-semibold text-gray-900">Statistik per Pertanyaan Tracer (dengan
                    Persentase)</h3>
                <div class="space-y-4">
                    @forelse ($this->tracerStatistikPertanyaan as $index => $pertanyaan)
                        <div class="overflow-hidden rounded-lg border border-gray-200"
                            wire:key="tracer-pertanyaan-{{ $index }}">
                            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">{{ $pertanyaan['label'] }}</p>
                                <p class="text-xs text-gray-500">Respon terisi:
                                    {{ number_format($pertanyaan['answered']) }}</p>
                            </div>
                            <table class="w-full">
                                <thead class="border-b border-gray-200 bg-gray-50/60">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700">Opsi
                                            Jawaban</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Jumlah
                                        </th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-700">Persentase
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($pertanyaan['options'] as $opsi)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $opsi['label'] }}</td>
                                            <td class="px-4 py-2 text-right text-sm font-semibold text-gray-900">
                                                {{ number_format($opsi['count']) }}</td>
                                            <td class="px-4 py-2 text-right text-sm font-semibold text-gray-900">
                                                {{ $opsi['percent'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                            Belum ada data tracer study untuk dihitung.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
