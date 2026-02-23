<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use App\Models\TracerSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public int $periodeTahun;

    public string $status = 'draft';

    public ?string $submittedAt = null;

    public ?string $a1StatusPerkawinan = null;

    public string $a2NegaraTinggal = 'Indonesia';

    public ?int $a3ProvinsiId = null;

    public ?int $a4KotaId = null;

    public string $a5EmailAktif = '';

    public string $a6NoHp = '';

    public ?string $b1StudiLanjut = null;

    public ?string $b2Bekerja = null;

    public ?string $b3BentukPekerjaan = null;

    public ?string $b4PenghasilanMin1Jam = null;

    public ?string $b5MembantuUsaha = null;

    public ?string $b6SementaraTidakBekerja = null;

    public ?string $c1WaktuPekerjaanPertama = null;

    public ?string $c2LokasiKerja = null;

    public string $c3Jabatan = '';

    public string $c4NamaPerusahaan = '';

    public string $c5NamaAtasan = '';

    public string $c6JabatanAtasan = '';

    public string $c7KontakAtasan = '';

    public ?string $c8JenisInstansi = null;

    public ?int $c9JamKerjaPerMinggu = null;

    public ?string $c10PenghasilanBulanan = null;

    public ?string $c11FrekuensiGantiKerja = null;

    public ?string $c12AlasanGantiKerja = null;

    public string $c12AlasanLainnya = '';

    public array $c13CaraDapatKerja = [];

    public string $c13CaraLainnya = '';

    public ?string $c14KesesuaianPekerjaan = null;

    public ?string $d1LokasiStudi = null;

    public ?string $d2Jenjang = null;

    public string $d3NamaPt = '';

    public string $d4ProgramStudi = '';

    public ?string $d5KesesuaianStudi = null;

    public ?string $d6MulaiStudi = null;

    public array $d7AlasanLanjut = [];

    public string $d7AlasanLainnya = '';

    public array $e1AktivitasMingguan = [];

    public array $e2AktivitasCariKerja = [];

    public ?int $e3LamaCariBulan = null;

    public array $e4AlasanMencari = [];

    public ?string $f1LokasiUsaha = null;

    public ?string $f2BentukUsaha = null;

    public string $f2BentukUsahaLainnya = '';

    public string $f3BidangUsaha = '';

    public ?string $f4ProdukUsaha = null;

    public ?string $f5Kepemilikan = null;

    public ?string $f6MulaiUsaha = null;

    public ?string $f7OmsetBulanan = null;

    public ?string $f8RiwayatGantiUsaha = null;

    public array $g1AlasanPilihSmk = [];

    public string $g1AlasanLainnya = '';

    public ?string $g2DurasiPkl = null;

    public ?string $g3KualitasPkl = null;

    public ?string $g4KesesuaianPkl = null;

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->isAlumni(), 403);

        $this->periodeTahun = now()->year;

        $submission = TracerSubmission::query()
            ->where('alumni_id', Auth::user()->alumni->id)
            ->where('periode_tahun', $this->periodeTahun)
            ->first();

        if ($submission) {
            $this->fillFromModel($submission);
        } else {
            $this->a5EmailAktif = Auth::user()->email ?? '';
            $this->a6NoHp = Auth::user()->alumni->nomor_telepon ?? '';
        }
    }

    #[Computed]
    public function countries()
    {
        return Country::query()->orderBy('nama')->get();
    }

    #[Computed]
    public function provinces()
    {
        return Province::query()->orderBy('nama')->get();
    }

    #[Computed]
    public function cities()
    {
        if (!$this->a3ProvinsiId) {
            return collect();
        }

        return City::query()->where('province_id', $this->a3ProvinsiId)->orderBy('nama')->get();
    }

    #[Computed]
    public function showSectionC(): bool
    {
        return $this->b2Bekerja === 'ya';
    }

    #[Computed]
    public function showSectionD(): bool
    {
        return $this->b1StudiLanjut === 'ya';
    }

    #[Computed]
    public function showSectionE(): bool
    {
        return $this->b1StudiLanjut === 'tidak' && $this->b2Bekerja === 'tidak';
    }

    #[Computed]
    public function showSectionF(): bool
    {
        return in_array($this->b3BentukPekerjaan, ['wirausaha_tanpa_pekerja', 'wirausaha_pekerja_tidak_dibayar', 'wirausaha_pekerja_dibayar'], true);
    }

    public function updatedA2NegaraTinggal(): void
    {
        if (strtolower($this->a2NegaraTinggal) !== 'indonesia') {
            $this->a3ProvinsiId = null;
            $this->a4KotaId = null;
        }
    }

    public function updatedA3ProvinsiId(): void
    {
        $this->a4KotaId = null;
    }

    public function updatedE1AktivitasMingguan(): void
    {
        $this->normalizeExclusiveSelection('e1AktivitasMingguan', 'tidak_termasuk');
    }

    public function updatedE2AktivitasCariKerja(): void
    {
        $this->normalizeExclusiveSelection('e2AktivitasCariKerja', 'tidak_melakukan');
    }

    protected function normalizeExclusiveSelection(string $field, string $exclusiveValue): void
    {
        $selectedValues = $this->{$field};

        if (!is_array($selectedValues)) {
            $this->{$field} = [];

            return;
        }

        $selectedValues = array_values(array_unique($selectedValues));

        if (in_array($exclusiveValue, $selectedValues, true) && count($selectedValues) > 1) {
            $selectedValues = [$exclusiveValue];
        }

        $this->{$field} = $selectedValues;
    }

    protected function exclusiveSelectionRule(string $exclusiveValue, string $message): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($exclusiveValue, $message): void {
            if (!is_array($value)) {
                return;
            }

            if (in_array($exclusiveValue, $value, true) && count($value) > 1) {
                $fail($message);
            }
        };
    }

    protected function rules(bool $isSubmit = false): array
    {
        $required = $isSubmit ? 'required' : 'nullable';

        $isIndonesia = strtolower($this->a2NegaraTinggal) === 'indonesia';
        $requireSectionC = $isSubmit && $this->showSectionC;
        $requireSectionD = $isSubmit && $this->showSectionD;
        $requireSectionE = $isSubmit && $this->showSectionE;
        $requireSectionF = $isSubmit && $this->showSectionF;
        $requireAlasanGantiKerja = $requireSectionC && in_array($this->c11FrekuensiGantiKerja, ['dua_kali', 'tiga_atau_lebih'], true);
        $requireC13Lainnya = $requireSectionC && in_array('lainnya', $this->c13CaraDapatKerja, true);
        $requireD7Lainnya = $requireSectionD && in_array('lainnya', $this->d7AlasanLanjut, true);
        $requireG1Lainnya = $isSubmit && in_array('lainnya', $this->g1AlasanPilihSmk, true);

        return [
            'a1StatusPerkawinan' => [$required, Rule::in(['belum_menikah', 'sudah_menikah', 'cerai'])],
            'a2NegaraTinggal' => [$required, 'string', 'max:100'],
            'a3ProvinsiId' => [Rule::requiredIf($isSubmit && $isIndonesia), 'nullable', 'integer', 'exists:provinces,id'],
            'a4KotaId' => [Rule::requiredIf($isSubmit && $isIndonesia && filled($this->a3ProvinsiId)), 'nullable', 'integer', Rule::exists('cities', 'id')->where(fn($query) => $query->where('province_id', $this->a3ProvinsiId))],
            'a5EmailAktif' => [$required, 'email', 'max:255'],
            'a6NoHp' => [$required, 'string', 'max:30'],
            'b1StudiLanjut' => [$required, Rule::in(['ya', 'tidak'])],
            'b2Bekerja' => [$required, Rule::in(['ya', 'tidak'])],
            'b3BentukPekerjaan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:100'],
            'b4PenghasilanMin1Jam' => [$required, Rule::in(['ya', 'tidak'])],
            'b5MembantuUsaha' => [$required, Rule::in(['ya', 'tidak'])],
            'b6SementaraTidakBekerja' => [$required, Rule::in(['ya', 'tidak'])],
            'c1WaktuPekerjaanPertama' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:50'],
            'c2LokasiKerja' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:50'],
            'c3Jabatan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:255'],
            'c4NamaPerusahaan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:255'],
            'c5NamaAtasan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:255'],
            'c6JabatanAtasan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:255'],
            'c7KontakAtasan' => ['nullable', 'string', 'max:255'],
            'c8JenisInstansi' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:100'],
            'c9JamKerjaPerMinggu' => [Rule::requiredIf($requireSectionC), 'nullable', 'integer', 'min:0', 'max:168'],
            'c10PenghasilanBulanan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:50'],
            'c11FrekuensiGantiKerja' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:50'],
            'c12AlasanGantiKerja' => [Rule::requiredIf($requireAlasanGantiKerja), 'nullable', 'string', 'max:255'],
            'c12AlasanLainnya' => [Rule::requiredIf($requireSectionC && $this->c12AlasanGantiKerja === 'lainnya'), 'nullable', 'string', 'max:255'],
            'c13CaraDapatKerja' => [Rule::requiredIf($requireSectionC), 'nullable', 'array', 'min:1'],
            'c13CaraLainnya' => [Rule::requiredIf($requireC13Lainnya), 'nullable', 'string', 'max:255'],
            'c14KesesuaianPekerjaan' => [Rule::requiredIf($requireSectionC), 'nullable', 'string', 'max:50'],
            'd1LokasiStudi' => [Rule::requiredIf($requireSectionD), 'nullable', 'string', 'max:50'],
            'd2Jenjang' => [Rule::requiredIf($requireSectionD), 'nullable', 'string', 'max:50'],
            'd3NamaPt' => [Rule::requiredIf($requireSectionD && $this->d2Jenjang === 's1'), 'nullable', 'string', 'max:255'],
            'd4ProgramStudi' => [Rule::requiredIf($requireSectionD && $this->d2Jenjang === 's1' && filled($this->d3NamaPt)), 'nullable', 'string', 'max:255'],
            'd5KesesuaianStudi' => [Rule::requiredIf($requireSectionD), 'nullable', 'string', 'max:80'],
            'd6MulaiStudi' => [Rule::requiredIf($requireSectionD), 'nullable', 'date'],
            'd7AlasanLanjut' => [Rule::requiredIf($requireSectionD), 'nullable', 'array', 'min:1'],
            'd7AlasanLainnya' => [Rule::requiredIf($requireD7Lainnya), 'nullable', 'string', 'max:255'],
            'e1AktivitasMingguan' => [Rule::requiredIf($requireSectionE), 'nullable', 'array', 'min:1', $this->exclusiveSelectionRule('tidak_termasuk', 'Jika memilih "Tidak termasuk semua pilihan di atas", jangan pilih aktivitas lainnya.')],
            'e2AktivitasCariKerja' => [Rule::requiredIf($requireSectionE), 'nullable', 'array', 'min:1', $this->exclusiveSelectionRule('tidak_melakukan', 'Jika memilih "Tidak melakukan semua kegiatan di atas", jangan pilih aktivitas lainnya.')],
            'e3LamaCariBulan' => [Rule::requiredIf($requireSectionE), 'nullable', 'integer', 'min:0', 'max:240'],
            'e4AlasanMencari' => [Rule::requiredIf($requireSectionE), 'nullable', 'array', 'min:1'],
            'f1LokasiUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'f2BentukUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'f2BentukUsahaLainnya' => [Rule::requiredIf($requireSectionF && $this->f2BentukUsaha === 'lainnya'), 'nullable', 'string', 'max:255'],
            'f3BidangUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:255'],
            'f4ProdukUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'f5Kepemilikan' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'f6MulaiUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'date'],
            'f7OmsetBulanan' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'f8RiwayatGantiUsaha' => [Rule::requiredIf($requireSectionF), 'nullable', 'string', 'max:50'],
            'g1AlasanPilihSmk' => [$required, 'array', Rule::requiredIf($isSubmit), 'min:1'],
            'g1AlasanLainnya' => [Rule::requiredIf($requireG1Lainnya), 'nullable', 'string', 'max:255'],
            'g2DurasiPkl' => [$required, 'string', 'max:50'],
            'g3KualitasPkl' => [$required, 'string', 'max:50'],
            'g4KesesuaianPkl' => [$required, 'string', 'max:50'],
        ];
    }

    protected function payload(string $status): array
    {
        return [
            'alumni_id' => Auth::user()->alumni->id,
            'periode_tahun' => $this->periodeTahun,
            'status' => $status,
            'submitted_at' => $status === 'submitted' ? now() : null,
            'a1_status_perkawinan' => $this->a1StatusPerkawinan,
            'a2_negara_tinggal' => $this->a2NegaraTinggal,
            'a3_provinsi_id' => strtolower($this->a2NegaraTinggal) === 'indonesia' ? $this->a3ProvinsiId : null,
            'a4_kota_id' => strtolower($this->a2NegaraTinggal) === 'indonesia' ? $this->a4KotaId : null,
            'a5_email_aktif' => $this->a5EmailAktif,
            'a6_no_hp' => $this->a6NoHp,
            'b1_studi_lanjut' => $this->b1StudiLanjut === null ? null : $this->b1StudiLanjut === 'ya',
            'b2_bekerja' => $this->b2Bekerja === null ? null : $this->b2Bekerja === 'ya',
            'b3_bentuk_pekerjaan' => $this->showSectionC ? $this->b3BentukPekerjaan : null,
            'b4_penghasilan_min_1jam' => $this->b4PenghasilanMin1Jam === null ? null : $this->b4PenghasilanMin1Jam === 'ya',
            'b5_membantu_usaha' => $this->b5MembantuUsaha === null ? null : $this->b5MembantuUsaha === 'ya',
            'b6_sementara_tidak_bekerja' => $this->b6SementaraTidakBekerja === null ? null : $this->b6SementaraTidakBekerja === 'ya',
            'c1_waktu_pekerjaan_pertama' => $this->showSectionC ? $this->c1WaktuPekerjaanPertama : null,
            'c2_lokasi_kerja' => $this->showSectionC ? $this->c2LokasiKerja : null,
            'c3_jabatan' => $this->showSectionC ? (filled($this->c3Jabatan) ? $this->c3Jabatan : null) : null,
            'c4_nama_perusahaan' => $this->showSectionC ? (filled($this->c4NamaPerusahaan) ? $this->c4NamaPerusahaan : null) : null,
            'c5_nama_atasan' => $this->showSectionC ? (filled($this->c5NamaAtasan) ? $this->c5NamaAtasan : null) : null,
            'c6_jabatan_atasan' => $this->showSectionC ? (filled($this->c6JabatanAtasan) ? $this->c6JabatanAtasan : null) : null,
            'c7_kontak_atasan' => $this->showSectionC ? (filled($this->c7KontakAtasan) ? $this->c7KontakAtasan : null) : null,
            'c8_jenis_instansi' => $this->showSectionC ? $this->c8JenisInstansi : null,
            'c9_jam_kerja_per_minggu' => $this->showSectionC ? $this->c9JamKerjaPerMinggu : null,
            'c10_penghasilan_bulanan' => $this->showSectionC ? $this->c10PenghasilanBulanan : null,
            'c11_frekuensi_ganti_kerja' => $this->showSectionC ? $this->c11FrekuensiGantiKerja : null,
            'c12_alasan_ganti_kerja' => $this->showSectionC ? $this->c12AlasanGantiKerja : null,
            'c12_alasan_lainnya' => $this->showSectionC ? (filled($this->c12AlasanLainnya) ? $this->c12AlasanLainnya : null) : null,
            'c13_cara_dapat_kerja' => $this->showSectionC ? $this->c13CaraDapatKerja : null,
            'c13_cara_lainnya' => $this->showSectionC ? (filled($this->c13CaraLainnya) ? $this->c13CaraLainnya : null) : null,
            'c14_kesesuaian_pekerjaan' => $this->showSectionC ? $this->c14KesesuaianPekerjaan : null,
            'd1_lokasi_studi' => $this->showSectionD ? $this->d1LokasiStudi : null,
            'd2_jenjang' => $this->showSectionD ? $this->d2Jenjang : null,
            'd3_nama_pt' => $this->showSectionD ? (filled($this->d3NamaPt) ? $this->d3NamaPt : null) : null,
            'd4_program_studi' => $this->showSectionD ? (filled($this->d4ProgramStudi) ? $this->d4ProgramStudi : null) : null,
            'd5_kesesuaian_studi' => $this->showSectionD ? $this->d5KesesuaianStudi : null,
            'd6_mulai_studi' => $this->showSectionD ? $this->d6MulaiStudi : null,
            'd7_alasan_lanjut' => $this->showSectionD ? $this->d7AlasanLanjut : null,
            'd7_alasan_lainnya' => $this->showSectionD ? (filled($this->d7AlasanLainnya) ? $this->d7AlasanLainnya : null) : null,
            'e1_aktivitas_mingguan' => $this->showSectionE ? $this->e1AktivitasMingguan : null,
            'e2_aktivitas_cari_kerja' => $this->showSectionE ? $this->e2AktivitasCariKerja : null,
            'e3_lama_cari_bulan' => $this->showSectionE ? $this->e3LamaCariBulan : null,
            'e4_alasan_mencari' => $this->showSectionE ? $this->e4AlasanMencari : null,
            'f1_lokasi_usaha' => $this->showSectionF ? $this->f1LokasiUsaha : null,
            'f2_bentuk_usaha' => $this->showSectionF ? $this->f2BentukUsaha : null,
            'f2_bentuk_usaha_lainnya' => $this->showSectionF ? (filled($this->f2BentukUsahaLainnya) ? $this->f2BentukUsahaLainnya : null) : null,
            'f3_bidang_usaha' => $this->showSectionF ? (filled($this->f3BidangUsaha) ? $this->f3BidangUsaha : null) : null,
            'f4_produk_usaha' => $this->showSectionF ? $this->f4ProdukUsaha : null,
            'f5_kepemilikan' => $this->showSectionF ? $this->f5Kepemilikan : null,
            'f6_mulai_usaha' => $this->showSectionF ? $this->f6MulaiUsaha : null,
            'f7_omset_bulanan' => $this->showSectionF ? $this->f7OmsetBulanan : null,
            'f8_riwayat_ganti_usaha' => $this->showSectionF ? $this->f8RiwayatGantiUsaha : null,
            'g1_alasan_pilih_smk' => $this->g1AlasanPilihSmk,
            'g1_alasan_lainnya' => filled($this->g1AlasanLainnya) ? $this->g1AlasanLainnya : null,
            'g2_durasi_pkl' => $this->g2DurasiPkl,
            'g3_kualitas_pkl' => $this->g3KualitasPkl,
            'g4_kesesuaian_pkl' => $this->g4KesesuaianPkl,
        ];
    }

    protected function fillFromModel(TracerSubmission $submission): void
    {
        $this->status = $submission->status;
        $this->submittedAt = $submission->submitted_at?->format('Y-m-d H:i:s');

        $this->a1StatusPerkawinan = $submission->a1_status_perkawinan;
        $this->a2NegaraTinggal = $submission->a2_negara_tinggal ?? 'Indonesia';
        $this->a3ProvinsiId = $submission->a3_provinsi_id;
        $this->a4KotaId = $submission->a4_kota_id;
        $this->a5EmailAktif = $submission->a5_email_aktif ?? '';
        $this->a6NoHp = $submission->a6_no_hp ?? '';

        $this->b1StudiLanjut = is_null($submission->b1_studi_lanjut) ? null : ($submission->b1_studi_lanjut ? 'ya' : 'tidak');
        $this->b2Bekerja = is_null($submission->b2_bekerja) ? null : ($submission->b2_bekerja ? 'ya' : 'tidak');
        $this->b3BentukPekerjaan = $submission->b3_bentuk_pekerjaan;
        $this->b4PenghasilanMin1Jam = is_null($submission->b4_penghasilan_min_1jam) ? null : ($submission->b4_penghasilan_min_1jam ? 'ya' : 'tidak');
        $this->b5MembantuUsaha = is_null($submission->b5_membantu_usaha) ? null : ($submission->b5_membantu_usaha ? 'ya' : 'tidak');
        $this->b6SementaraTidakBekerja = is_null($submission->b6_sementara_tidak_bekerja) ? null : ($submission->b6_sementara_tidak_bekerja ? 'ya' : 'tidak');

        $this->c1WaktuPekerjaanPertama = $submission->c1_waktu_pekerjaan_pertama;
        $this->c2LokasiKerja = $submission->c2_lokasi_kerja;
        $this->c3Jabatan = $submission->c3_jabatan ?? '';
        $this->c4NamaPerusahaan = $submission->c4_nama_perusahaan ?? '';
        $this->c5NamaAtasan = $submission->c5_nama_atasan ?? '';
        $this->c6JabatanAtasan = $submission->c6_jabatan_atasan ?? '';
        $this->c7KontakAtasan = $submission->c7_kontak_atasan ?? '';
        $this->c8JenisInstansi = $submission->c8_jenis_instansi;
        $this->c9JamKerjaPerMinggu = $submission->c9_jam_kerja_per_minggu;
        $this->c10PenghasilanBulanan = $submission->c10_penghasilan_bulanan;
        $this->c11FrekuensiGantiKerja = $submission->c11_frekuensi_ganti_kerja;
        $this->c12AlasanGantiKerja = $submission->c12_alasan_ganti_kerja;
        $this->c12AlasanLainnya = $submission->c12_alasan_lainnya ?? '';
        $this->c13CaraDapatKerja = $submission->c13_cara_dapat_kerja ?? [];
        $this->c13CaraLainnya = $submission->c13_cara_lainnya ?? '';
        $this->c14KesesuaianPekerjaan = $submission->c14_kesesuaian_pekerjaan;

        $this->d1LokasiStudi = $submission->d1_lokasi_studi;
        $this->d2Jenjang = $submission->d2_jenjang;
        $this->d3NamaPt = $submission->d3_nama_pt ?? '';
        $this->d4ProgramStudi = $submission->d4_program_studi ?? '';
        $this->d5KesesuaianStudi = $submission->d5_kesesuaian_studi;
        $this->d6MulaiStudi = $submission->d6_mulai_studi?->format('Y-m-d');
        $this->d7AlasanLanjut = $submission->d7_alasan_lanjut ?? [];
        $this->d7AlasanLainnya = $submission->d7_alasan_lainnya ?? '';

        $this->e1AktivitasMingguan = $submission->e1_aktivitas_mingguan ?? [];
        $this->e2AktivitasCariKerja = $submission->e2_aktivitas_cari_kerja ?? [];
        $this->e3LamaCariBulan = $submission->e3_lama_cari_bulan;
        $this->e4AlasanMencari = $submission->e4_alasan_mencari ?? [];

        $this->normalizeExclusiveSelection('e1AktivitasMingguan', 'tidak_termasuk');
        $this->normalizeExclusiveSelection('e2AktivitasCariKerja', 'tidak_melakukan');

        $this->f1LokasiUsaha = $submission->f1_lokasi_usaha;
        $this->f2BentukUsaha = $submission->f2_bentuk_usaha;
        $this->f2BentukUsahaLainnya = $submission->f2_bentuk_usaha_lainnya ?? '';
        $this->f3BidangUsaha = $submission->f3_bidang_usaha ?? '';
        $this->f4ProdukUsaha = $submission->f4_produk_usaha;
        $this->f5Kepemilikan = $submission->f5_kepemilikan;
        $this->f6MulaiUsaha = $submission->f6_mulai_usaha?->format('Y-m-d');
        $this->f7OmsetBulanan = $submission->f7_omset_bulanan;
        $this->f8RiwayatGantiUsaha = $submission->f8_riwayat_ganti_usaha;

        $this->g1AlasanPilihSmk = $submission->g1_alasan_pilih_smk ?? [];
        $this->g1AlasanLainnya = $submission->g1_alasan_lainnya ?? '';
        $this->g2DurasiPkl = $submission->g2_durasi_pkl;
        $this->g3KualitasPkl = $submission->g3_kualitas_pkl;
        $this->g4KesesuaianPkl = $submission->g4_kesesuaian_pkl;
    }

    public function saveDraft(): void
    {
        $this->validate($this->rules(false));

        TracerSubmission::query()->updateOrCreate(
            [
                'alumni_id' => Auth::user()->alumni->id,
                'periode_tahun' => $this->periodeTahun,
            ],
            $this->payload('draft'),
        );

        $this->status = 'draft';
        $this->submittedAt = null;

        $this->dispatch('toast', message: 'Draft tracer study berhasil disimpan.', type: 'success');
    }

    public function submitFinal(): void
    {
        $this->validate($this->rules(true));

        TracerSubmission::query()->updateOrCreate(
            [
                'alumni_id' => Auth::user()->alumni->id,
                'periode_tahun' => $this->periodeTahun,
            ],
            $this->payload('submitted'),
        );

        $this->status = 'submitted';
        $this->submittedAt = now()->format('Y-m-d H:i:s');

        $this->dispatch('toast', message: 'Tracer study berhasil dikirim.', type: 'success');
    }

    #[Computed]
    public function sectionProgress(): array
    {
        $isFilled = fn($value) => !blank($value);

        $sections = [
            'A' => $isFilled($this->a1StatusPerkawinan) && $isFilled($this->a2NegaraTinggal) && $isFilled($this->a5EmailAktif) && $isFilled($this->a6NoHp),
            'B' => $isFilled($this->b1StudiLanjut) && $isFilled($this->b2Bekerja) && $isFilled($this->b4PenghasilanMin1Jam) && $isFilled($this->b5MembantuUsaha) && $isFilled($this->b6SementaraTidakBekerja),
            'C' => !$this->showSectionC || ($isFilled($this->c1WaktuPekerjaanPertama) && $isFilled($this->c2LokasiKerja) && $isFilled($this->c3Jabatan) && $isFilled($this->c4NamaPerusahaan) && $isFilled($this->c8JenisInstansi) && $isFilled($this->c9JamKerjaPerMinggu) && $isFilled($this->c10PenghasilanBulanan) && $isFilled($this->c11FrekuensiGantiKerja) && !empty($this->c13CaraDapatKerja) && $isFilled($this->c14KesesuaianPekerjaan)),
            'D' => !$this->showSectionD || ($isFilled($this->d1LokasiStudi) && $isFilled($this->d2Jenjang) && $isFilled($this->d5KesesuaianStudi) && $isFilled($this->d6MulaiStudi) && !empty($this->d7AlasanLanjut)),
            'E' => !$this->showSectionE || (!empty($this->e1AktivitasMingguan) && !empty($this->e2AktivitasCariKerja) && $isFilled($this->e3LamaCariBulan) && !empty($this->e4AlasanMencari)),
            'F' => !$this->showSectionF || ($isFilled($this->f1LokasiUsaha) && $isFilled($this->f2BentukUsaha) && $isFilled($this->f3BidangUsaha) && $isFilled($this->f4ProdukUsaha) && $isFilled($this->f5Kepemilikan) && $isFilled($this->f6MulaiUsaha) && $isFilled($this->f7OmsetBulanan) && $isFilled($this->f8RiwayatGantiUsaha)),
            'G' => !empty($this->g1AlasanPilihSmk) && $isFilled($this->g2DurasiPkl) && $isFilled($this->g3KualitasPkl) && $isFilled($this->g4KesesuaianPkl),
        ];

        $completed = collect($sections)->filter()->count();

        return [
            'sections' => $sections,
            'completed' => $completed,
            'total' => count($sections),
            'percent' => (int) round(($completed / max(count($sections), 1)) * 100),
        ];
    }
};
?>


<div>
    <style>
        .tracer-form :is(input, select, textarea):has(+ p.text-red-600) {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 2px rgba(252, 165, 165, 0.45) !important;
        }
    </style>

    <script>
        window.tracerScrollOnError = false;

        const scrollToFirstTracerError = () => {
            const firstError = document.querySelector('.tracer-form p.text-red-600');

            if (!firstError) {
                return;
            }

            const field = firstError.previousElementSibling;
            const isFormControl = field && ['INPUT', 'SELECT', 'TEXTAREA'].includes(field.tagName);
            const target = isFormControl ? field : firstError;

            target.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            if (isFormControl && typeof field.focus === 'function') {
                field.focus({
                    preventScroll: true
                });
            }
        };

        document.addEventListener('livewire:init', () => {
            Livewire.hook('morphed', () => {
                if (!window.tracerScrollOnError) {
                    return;
                }

                scrollToFirstTracerError();
                window.tracerScrollOnError = false;
            });
        });
    </script>
    <div class="tracer-form space-y-6">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Form Tracer Study Alumni</h1>
                    <p class="mt-1 text-sm text-gray-600">Periode {{ $periodeTahun }} · Status: <span
                            class="font-semibold">{{ strtoupper($status) }}</span></p>
                </div>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                    <button wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:opacity-60 sm:w-auto">
                        <span wire:loading.remove wire:target="saveDraft">Simpan Draft</span>
                        <span wire:loading wire:target="saveDraft">Menyimpan...</span>
                    </button>
                    <button wire:click="submitFinal" wire:loading.attr="disabled" wire:target="submitFinal"
                        x-on:click="window.tracerScrollOnError = true"
                        class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60 sm:w-auto">
                        <span wire:loading.remove wire:target="submitFinal">Kirim Final</span>
                        <span wire:loading wire:target="submitFinal">Mengirim...</span>
                    </button>
                </div>
            </div>
            @if ($submittedAt)
                <p class="mt-3 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">Form
                    sudah
                    dikirim pada {{ \Illuminate\Support\Carbon::parse($submittedAt)->format('d M Y H:i') }}</p>
            @endif

            @if ($errors->any())
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="mb-1 font-semibold">Masih ada data yang belum valid:</p>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="sticky top-2 z-20 mt-4 rounded-xl border border-gray-200 bg-gray-50 p-3 sm:static">
                <div class="mb-2 flex items-center justify-between text-xs font-semibold text-gray-600">
                    <span>Progress Pengisian</span>
                    <span>{{ $this->sectionProgress['completed'] }}/{{ $this->sectionProgress['total'] }} section</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full bg-blue-600 transition-all"
                        style="width: {{ $this->sectionProgress['percent'] }}%"></div>
                </div>
                <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                    @foreach ($this->sectionProgress['sections'] as $section => $done)
                        <button type="button"
                            onclick="document.getElementById('section-{{ strtolower($section) }}')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                            class="inline-flex shrink-0 items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $done ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-500' }}">
                            <span>{{ $section }}</span>
                            <span>{{ $done ? '✓' : '•' }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="section-a"
            class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
            @php
                $aHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'a'));
            @endphp
            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                A. Data Umum Alumni
                @if ($aHasErrors)
                    <span
                        class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                        dicek</span>
                @endif
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">A1. Status Perkawinan</label>
                    <p class="mb-2 text-xs text-gray-500">Bagaimana status perkawinan anda saat ini?</p>
                    <select wire:model.live="a1StatusPerkawinan"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih status</option>
                        <option value="belum_menikah">Belum menikah</option>
                        <option value="sudah_menikah">Sudah menikah</option>
                        <option value="cerai">Cerai</option>
                    </select>
                    @error('a1StatusPerkawinan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">A2. Negara Tinggal</label>
                    <p class="mb-2 text-xs text-gray-500">Di negara mana anda tinggal saat ini?</p>
                    <select wire:model.live="a2NegaraTinggal"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih negara</option>
                        @foreach ($this->countries as $country)
                            <option value="{{ $country->nama }}">{{ $country->nama }}</option>
                        @endforeach
                    </select>
                    @error('a2NegaraTinggal')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @if (strtolower($a2NegaraTinggal) === 'indonesia')
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">A3. Provinsi Tinggal</label>
                        <p class="mb-2 text-xs text-gray-500">Di provinsi mana anda tinggal saat ini?</p>
                        <select wire:model.live="a3ProvinsiId"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih provinsi</option>
                            @foreach ($this->provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->nama }}</option>
                            @endforeach
                        </select>
                        @error('a3ProvinsiId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">A4. Kabupaten/Kota Tinggal</label>
                        <p class="mb-2 text-xs text-gray-500">Di kabupaten/kota mana anda tinggal saat ini?</p>
                        <select wire:model.defer="a4KotaId"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500"
                            @disabled(!$a3ProvinsiId)>
                            <option value="">Pilih kab/kota</option>
                            @foreach ($this->cities as $city)
                                <option value="{{ $city->id }}">{{ $city->nama }}</option>
                            @endforeach
                        </select>
                        @error('a4KotaId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">A5. Email Aktif</label>
                    <p class="mb-2 text-xs text-gray-500">Apa alamat email aktif yang bisa dihubungi?</p>
                    <input type="email" wire:model.defer="a5EmailAktif"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    @error('a5EmailAktif')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">A6. Nomor HP / WhatsApp</label>
                    <p class="mb-2 text-xs text-gray-500">Berapa nomor HP/WhatsApp aktif anda?</p>
                    <input type="text" wire:model.defer="a6NoHp"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    @error('a6NoHp')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div id="section-b"
            class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
            @php
                $bHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'b'));
            @endphp
            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                B. Status Kegiatan Lulusan
                @if ($bHasErrors)
                    <span
                        class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                        dicek</span>
                @endif
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B1. Studi Lanjut</label>
                    <p class="mb-2 text-xs text-gray-500">Apakah anda melanjutkan studi?</p>
                    <select wire:model.live="b1StudiLanjut"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option> 
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                    @error('b1StudiLanjut')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B2. Bekerja / Berwirausaha</label>
                    <p class="mb-2 text-xs text-gray-500">Apakah anda saat ini bekerja atau berwirausaha?</p>
                    <select wire:model.live="b2Bekerja"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                    @error('b2Bekerja')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B3. Bentuk Pekerjaan</label>
                    <p class="mb-2 text-xs text-gray-500">Apa bentuk pekerjaan utama anda saat ini?</p>
                    @php
                        $isB3Disabled = $b2Bekerja === 'tidak';
                    @endphp
                    <select wire:model.live="b3BentukPekerjaan"
                        class="w-full rounded-lg border px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 {{ $isB3Disabled ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-400 grayscale' : 'border-gray-300' }}"
                        @disabled($isB3Disabled)>
                        <option value="">Pilih bentuk</option>
                        <option value="wirausaha_tanpa_pekerja">Wirausaha sendiri tanpa pekerja</option>
                        <option value="wirausaha_pekerja_tidak_dibayar">Wirausaha dengan pekerja tidak dibayar</option>
                        <option value="wirausaha_pekerja_dibayar">Wirausaha dengan pekerja dibayar</option>
                        <option value="membantu_usaha_keluarga">Membantu usaha keluarga</option>
                        <option value="buruh_karyawan_pegawai">Buruh / Karyawan / Pegawai</option>
                        <option value="pekerja_bebas">Pekerja bebas</option>
                    </select>
                    @error('b3BentukPekerjaan')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B4. Bekerja minimal 1 jam untuk
                        penghasilan</label>
                    <p class="mb-2 text-xs text-gray-500">Apakah anda bekerja minimal 1 jam untuk memperoleh penghasilan?</p>
                    <select wire:model.defer="b4PenghasilanMin1Jam"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                    @error('b4PenghasilanMin1Jam')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B5. Membantu usaha keluarga</label>
                    <p class="mb-2 text-xs text-gray-500">Apakah anda membantu usaha keluarga?</p>
                    <select wire:model.defer="b5MembantuUsaha"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                    @error('b5MembantuUsaha')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">B6. Punya usaha tetapi sementara tidak
                        bekerja</label>
                    <p class="mb-2 text-xs text-gray-500">Apakah anda punya usaha tetapi sementara tidak bekerja?</p>
                    <select wire:model.defer="b6SementaraTidakBekerja"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                    @error('b6SementaraTidakBekerja')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        @if ($this->showSectionC)
            <div id="section-c"
                class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
                @php
                    $cHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'c'));
                @endphp
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    C. Data Pekerjaan
                    @if ($cHasErrors)
                        <span
                            class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                            dicek</span>
                    @endif
                </h2>
                <p class="mb-4 text-sm text-gray-600">Bagian ini tampil karena B2 = Ya.</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C1. Waktu Mendapatkan
                            Pekerjaan</label>
                        <p class="mb-2 text-xs text-gray-500">Kapan anda mendapatkan pekerjaan pertama?</p>
                        <select wire:model.defer="c1WaktuPekerjaanPertama"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="sebelum_lulus">Sebelum lulus</option>
                            <option value="setelah_lulus">Setelah lulus</option>
                        </select>
                        @error('c1WaktuPekerjaanPertama')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C2. Lokasi Tempat Kerja</label>
                        <p class="mb-2 text-xs text-gray-500">Di mana lokasi tempat kerja anda?</p>
                        <select wire:model.defer="c2LokasiKerja"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="dalam_negeri">Dalam negeri</option>
                            <option value="luar_negeri">Luar negeri</option>
                        </select>
                        @error('c2LokasiKerja')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C3. Jabatan / Posisi</label>
                        <p class="mb-2 text-xs text-gray-500">Apa jabatan atau posisi pekerjaan anda saat ini?</p><input type="text" wire:model.defer="c3Jabatan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('c3Jabatan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C4. Nama Perusahaan</label>
                        <p class="mb-2 text-xs text-gray-500">Di perusahaan/lembaga mana anda bekerja?</p><input type="text" wire:model.defer="c4NamaPerusahaan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('c4NamaPerusahaan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C5. Nama Atasan
                            Langsung</label>
                        <p class="mb-2 text-xs text-gray-500">Siapa nama atasan langsung anda?</p><input type="text" wire:model.defer="c5NamaAtasan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C6. Jabatan Atasan</label>
                        <p class="mb-2 text-xs text-gray-500">Apa jabatan atasan langsung anda?</p><input type="text" wire:model.defer="c6JabatanAtasan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C7. Kontak Atasan</label>
                        <p class="mb-2 text-xs text-gray-500">Bagaimana kontak atasan langsung anda?</p><input type="text" wire:model.defer="c7KontakAtasan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C8. Jenis Instansi</label>
                        <p class="mb-2 text-xs text-gray-500">Instansi tempat anda bekerja termasuk jenis apa?</p>
                        <select wire:model.defer="c8JenisInstansi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="instansi_pemerintah">Instansi pemerintah</option>
                            <option value="lembaga_internasional">Lembaga internasional</option>
                            <option value="lembaga_non_profit">Lembaga non-profit</option>
                            <option value="perusahaan_swasta_bumn_bumd">Perusahaan swasta / BUMN / BUMD</option>
                            <option value="koperasi">Koperasi</option>
                            <option value="usaha_perorangan">Usaha perorangan</option>
                            <option value="rumah_tangga">Rumah tangga</option>
                        </select>
                        @error('c8JenisInstansi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C9. Jam Kerja per
                            Minggu</label>
                        <p class="mb-2 text-xs text-gray-500">Berapa total jam kerja anda dalam 1 minggu?</p><input type="number" wire:model.defer="c9JamKerjaPerMinggu"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('c9JamKerjaPerMinggu')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C10. Penghasilan Bulanan</label>
                        <p class="mb-2 text-xs text-gray-500">Berapa kisaran penghasilan bulanan anda?</p>
                        <select wire:model.defer="c10PenghasilanBulanan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="kurang_3_juta">&lt; 3 juta</option>
                            <option value="3_5_juta">3 - 5 juta</option>
                            <option value="lebih_5_juta">&gt; 5 juta</option>
                        </select>
                        @error('c10PenghasilanBulanan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C11. Frekuensi Ganti
                            Pekerjaan</label>
                        <p class="mb-2 text-xs text-gray-500">Seberapa sering anda berganti pekerjaan?</p>
                        <select wire:model.defer="c11FrekuensiGantiKerja"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="belum_pernah">Belum pernah</option>
                            <option value="satu_kali">Satu kali</option>
                            <option value="dua_kali">Dua kali</option>
                            <option value="tiga_atau_lebih">Tiga kali atau lebih</option>
                        </select>
                        @error('c11FrekuensiGantiKerja')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C12. Alasan Ganti Pekerjaan</label>
                        <p class="mb-2 text-xs text-gray-500">Apa alasan utama anda berganti pekerjaan?</p>
                        <select wire:model.defer="c12AlasanGantiKerja"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="phk">Di-PHK dari pekerjaan sebelumnya</option>
                            <option value="gaji_kurang">Gaji/penghasilan kurang</option>
                            <option value="beban_berat">Beban pekerjaan terlalu berat</option>
                            <option value="kurang_menantang">Pekerjaan kurang menantang</option>
                            <option value="karir_sulit">Jenjang karir sulit berkembang</option>
                            <option value="iklim_kerja">Iklim kerja kurang kondusif</option>
                            <option value="kontrak_selesai">Kontrak selesai</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('c12AlasanGantiKerja')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C12. Alasan Lainnya</label>
                        <p class="mb-2 text-xs text-gray-500">Jika alasan lainnya, tuliskan secara singkat.</p><input type="text" wire:model.defer="c12AlasanLainnya"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('c12AlasanLainnya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">C13. Cara Mendapatkan Pekerjaan
                            Pertama</label>
                        <p class="mb-2 text-xs text-gray-500">Bagaimana cara anda mendapatkan pekerjaan pertama?</p>
                        <div
                            class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach (['mitra_smk' => 'Melalui industri mitra SMK', 'bkk_smk' => 'Melalui bursa kerja SMK', 'tempat_magang' => 'Melalui tempat PKL', 'ikatan_alumni' => 'Melalui ikatan alumni', 'iklan' => 'Melalui iklan media', 'job_fair' => 'Melalui job fair', 'dinas_tk' => 'Melalui dinas ketenagakerjaan', 'bantuan_orang_lain' => 'Bantuan orang lain/keluarga', 'lainnya' => 'Lainnya'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                            type="checkbox" wire:model.defer="c13CaraDapatKerja"
                                            value="{{ $value }}"
                                            class="rounded border-gray-300" />{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error('c13CaraDapatKerja')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">C13. Cara Lainnya</label>
                        <p class="mb-2 text-xs text-gray-500">Jika cara lainnya, tuliskan secara singkat.</p><input type="text" wire:model.defer="c13CaraLainnya"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('c13CaraLainnya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">C14. Kesesuaian Pekerjaan</label>
                        <p class="mb-2 text-xs text-gray-500">Seberapa sesuai pekerjaan anda dengan kompetensi SMK?</p>
                        <select wire:model.defer="c14KesesuaianPekerjaan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="sangat_tidak_selaras">Sangat tidak selaras</option>
                            <option value="tidak_selaras">Tidak selaras</option>
                            <option value="selaras">Selaras</option>
                            <option value="sangat_selaras">Sangat selaras</option>
                        </select>
                        @error('c14KesesuaianPekerjaan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        @if ($this->showSectionD)
            <div id="section-d"
                class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
                @php
                    $dHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'd'));
                @endphp
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    D. Studi Lanjut
                    @if ($dHasErrors)
                        <span
                            class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                            dicek</span>
                    @endif
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D1. Lokasi Studi</label>
                        <p class="mb-2 text-xs text-gray-500">Di mana lokasi studi lanjutan anda?</p><select wire:model.defer="d1LokasiStudi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="dalam_negeri">Dalam negeri</option>
                            <option value="luar_negeri">Luar negeri</option>
                        </select>
                        @error('d1LokasiStudi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D2. Jenjang
                            Pendidikan</label>
                        <p class="mb-2 text-xs text-gray-500">Apa jenjang pendidikan yang sedang/akan anda tempuh?</p><select wire:model.defer="d2Jenjang"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="d1">D1</option>
                            <option value="d2">D2</option>
                            <option value="d3">D3</option>
                            <option value="d4">D4 / Sarjana Terapan</option>
                            <option value="s1">S1</option>
                        </select>
                        @error('d2Jenjang')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D3. Nama Perguruan
                            Tinggi</label>
                        <p class="mb-2 text-xs text-gray-500">Apa nama perguruan tinggi tempat anda studi?</p><input type="text" wire:model.defer="d3NamaPt"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('d3NamaPt')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D4. Program Studi</label>
                        <p class="mb-2 text-xs text-gray-500">Apa program studi/jurusan yang anda ambil?</p><input type="text" wire:model.defer="d4ProgramStudi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('d4ProgramStudi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D5. Kesesuaian
                            Studi</label>
                        <p class="mb-2 text-xs text-gray-500">Seberapa sesuai studi lanjutan anda dengan kompetensi SMK?</p><select wire:model.defer="d5KesesuaianStudi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="sangat_tidak_selaras">Sangat tidak selaras</option>
                            <option value="tidak_selaras">Tidak selaras</option>
                            <option value="selaras">Selaras</option>
                            <option value="sangat_selaras">Sangat selaras</option>
                        </select>
                        @error('d5KesesuaianStudi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D6. Waktu Mulai
                            Studi</label>
                        <p class="mb-2 text-xs text-gray-500">Kapan anda mulai melanjutkan studi?</p><input type="date" wire:model.defer="d6MulaiStudi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('d6MulaiStudi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">D7. Alasan Melanjutkan
                            Studi</label>
                        <p class="mb-2 text-xs text-gray-500">Apa alasan utama anda melanjutkan studi?</p>
                        <div
                            class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach (['meningkatkan_kompetensi' => 'Meningkatkan kompetensi', 'status_sosial' => 'Meningkatkan status sosial', 'beasiswa' => 'Memperoleh beasiswa', 'saran_orangtua' => 'Saran orang tua/keluarga', 'belum_dapat_kerja' => 'Belum menemukan pekerjaan tepat', 'lainnya' => 'Lainnya'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                            type="checkbox" wire:model.defer="d7AlasanLanjut"
                                            value="{{ $value }}"
                                            class="rounded border-gray-300" />{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error('d7AlasanLanjut')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">D7. Alasan Lainnya</label>
                        <p class="mb-2 text-xs text-gray-500">Jika alasan lainnya, tuliskan secara singkat.</p><input type="text" wire:model.defer="d7AlasanLainnya"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('d7AlasanLainnya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        @if ($this->showSectionE)
            <div id="section-e"
                class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
                @php
                    $eHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'e'));
                @endphp
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    E. Belum Bekerja
                    @if ($eHasErrors)
                        <span
                            class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                            dicek</span>
                    @endif
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">E1. Aktivitas Mingguan</label>
                        <p class="mb-2 text-xs text-gray-500">Apa aktivitas utama anda dalam satu minggu terakhir?</p>
                        <div
                            class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach (['rumah_tangga' => 'Mengurus rumah tangga', 'pelatihan' => 'Mengikuti pelatihan/kursus', 'persiapan_studi' => 'Mempersiapkan studi', 'organisasi_sosial' => 'Terlibat organisasi sosial', 'tidak_termasuk' => 'Tidak termasuk semua pilihan di atas'] as $value => $label)
                                    @php
                                        $e1HasExclusive = in_array('tidak_termasuk', $e1AktivitasMingguan, true);
                                        $e1HasOtherSelected = !empty(
                                            array_diff($e1AktivitasMingguan, ['tidak_termasuk'])
                                        );
                                    @endphp
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                            type="checkbox" wire:model.live="e1AktivitasMingguan"
                                            value="{{ $value }}" @disabled(($value !== 'tidak_termasuk' && $e1HasExclusive) || ($value === 'tidak_termasuk' && $e1HasOtherSelected))
                                            class="rounded border-gray-300" />{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error('e1AktivitasMingguan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">E2. Aktivitas Mencari Kerja</label>
                        <p class="mb-2 text-xs text-gray-500">Aktivitas apa yang anda lakukan untuk mencari kerja/usaha?</p>
                        <div
                            class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach (['kirim_lamaran' => 'Mempersiapkan/mengirim lamaran', 'ikut_seleksi' => 'Mengikuti seleksi kerja', 'menunggu_hasil' => 'Menunggu hasil lamaran', 'modal_usaha' => 'Mengumpulkan modal usaha', 'lokasi_usaha' => 'Mencari lokasi usaha', 'izin_usaha' => 'Mengurus izin usaha', 'tidak_melakukan' => 'Tidak melakukan semua kegiatan di atas'] as $value => $label)
                                    @php
                                        $e2HasExclusive = in_array('tidak_melakukan', $e2AktivitasCariKerja, true);
                                        $e2HasOtherSelected = !empty(
                                            array_diff($e2AktivitasCariKerja, ['tidak_melakukan'])
                                        );
                                    @endphp
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                            type="checkbox" wire:model.live="e2AktivitasCariKerja"
                                            value="{{ $value }}" @disabled(($value !== 'tidak_melakukan' && $e2HasExclusive) || ($value === 'tidak_melakukan' && $e2HasOtherSelected))
                                            class="rounded border-gray-300" />{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error('e2AktivitasCariKerja')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">E3. Lama Mencari Kerja
                            (bulan)</label>
                        <p class="mb-2 text-xs text-gray-500">Sudah berapa lama anda mencari pekerjaan (dalam bulan)?</p><input type="number" wire:model.defer="e3LamaCariBulan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('e3LamaCariBulan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium text-gray-700">E4. Alasan Mencari
                            Pekerjaan</label>
                        <p class="mb-2 text-xs text-gray-500">Apa alasan utama anda sedang mencari pekerjaan?</p>
                        <div
                            class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                @foreach (['tidak_sesuai_keahlian' => 'Pekerjaan sebelumnya kurang sesuai bidang keahlian', 'tidak_lanjut_kuliah' => 'Tidak melanjutkan kuliah', 'upah_kurang' => 'Upah kurang layak', 'phk' => 'PHK', 'usaha_bangkrut' => 'Usaha bangkrut', 'kontrak_habis' => 'Masa kontrak habis'] as $value => $label)
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                            type="checkbox" wire:model.defer="e4AlasanMencari"
                                            value="{{ $value }}"
                                            class="rounded border-gray-300" />{{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        @error('e4AlasanMencari')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        @if ($this->showSectionF)
            <div id="section-f"
                class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
                @php
                    $fHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'f'));
                @endphp
                <h2 class="mb-4 text-lg font-semibold text-gray-900">
                    F. Data Wirausaha
                    @if ($fHasErrors)
                        <span
                            class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                            dicek</span>
                    @endif
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F1. Lokasi Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Di mana lokasi usaha anda dijalankan?</p><select wire:model.defer="f1LokasiUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="dalam_negeri">Dalam negeri</option>
                            <option value="luar_negeri">Luar negeri</option>
                        </select>
                        @error('f1LokasiUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F2. Bentuk Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Apa bentuk badan usaha anda?</p><select wire:model.defer="f2BentukUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="perorangan">Usaha perorangan</option>
                            <option value="koperasi">Koperasi</option>
                            <option value="firma">Firma</option>
                            <option value="cv">CV</option>
                            <option value="pt">PT</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('f2BentukUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F2. Bentuk Usaha
                            Lainnya</label>
                        <p class="mb-2 text-xs text-gray-500">Jika bentuk usaha lainnya, tuliskan secara singkat.</p><input type="text" wire:model.defer="f2BentukUsahaLainnya"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('f2BentukUsahaLainnya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F3. Bidang Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Apa bidang usaha utama anda?</p><input type="text" wire:model.defer="f3BidangUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('f3BidangUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F4. Produk Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Produk usaha anda berupa barang, jasa, atau keduanya?</p><select wire:model.defer="f4ProdukUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="barang">Barang</option>
                            <option value="jasa">Jasa</option>
                            <option value="barang_jasa">Barang dan jasa</option>
                        </select>
                        @error('f4ProdukUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F5. Kepemilikan
                            Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Status kepemilikan usaha anda bagaimana?</p><select wire:model.defer="f5Kepemilikan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="milik_sendiri">Milik sendiri</option>
                            <option value="milik_bersama">Milik bersama</option>
                        </select>
                        @error('f5Kepemilikan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F6. Waktu Mulai
                            Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Kapan anda mulai menjalankan usaha ini?</p><input type="date" wire:model.defer="f6MulaiUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                        @error('f6MulaiUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F7. Omset Bulanan</label>
                        <p class="mb-2 text-xs text-gray-500">Berapa kisaran omset bulanan usaha anda?</p><select wire:model.defer="f7OmsetBulanan"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="kurang_25_jt">Kurang dari 25jt</option>
                            <option value="25_50_jt">25jt - 50jt</option>
                            <option value="50_100_jt">50jt - 100jt</option>
                            <option value="lebih_100_jt">Lebih dari 100jt</option>
                        </select>
                        @error('f7OmsetBulanan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="mb-1 block text-sm font-medium text-gray-700">F8. Riwayat Pergantian
                            Usaha</label>
                        <p class="mb-2 text-xs text-gray-500">Seberapa sering anda mengganti jenis/usaha sebelumnya?</p><select wire:model.defer="f8RiwayatGantiUsaha"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih</option>
                            <option value="belum_pernah">Belum pernah</option>
                            <option value="1x">1x</option>
                            <option value="2x">2x</option>
                            <option value="3x_atau_lebih">3x atau lebih</option>
                        </select>
                        @error('f8RiwayatGantiUsaha')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <div id="section-g"
            class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:scroll-mt-6">
            @php
                $gHasErrors = collect($errors->keys())->contains(fn($key) => str_starts_with($key, 'g'));
            @endphp
            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                G. Feedback terhadap SMK
                @if ($gHasErrors)
                    <span
                        class="ml-2 rounded border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700">Perlu
                        dicek</span>
                @endif
            </h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">G1. Alasan Memilih SMK</label>
                    <p class="mb-2 text-xs text-gray-500">Apa alasan utama anda memilih sekolah di SMK?</p>
                    <div
                        class="max-h-52 space-y-2 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-3 md:max-h-none md:overflow-visible">
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            @foreach (['cepat_kerja' => 'Ingin cepat dapat pekerjaan', 'keinginan_sendiri' => 'Keinginan sendiri', 'diajak_teman' => 'Diajak teman', 'keinginan_orangtua' => 'Keinginan orang tua/keluarga', 'tidak_diterima_lain' => 'Tidak diterima di sekolah lain', 'lainnya' => 'Lainnya'] as $value => $label)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700"><input
                                        type="checkbox" wire:model.defer="g1AlasanPilihSmk"
                                        value="{{ $value }}"
                                        class="rounded border-gray-300" />{{ $label }}</label>
                            @endforeach
                        </div>
                    </div>
                    @error('g1AlasanPilihSmk')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">G1. Alasan Lainnya</label>
                    <p class="mb-2 text-xs text-gray-500">Jika alasan lainnya, tuliskan secara singkat.</p><input type="text" wire:model.defer="g1AlasanLainnya"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                    @error('g1AlasanLainnya')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">G2. Durasi PKL</label>
                    <p class="mb-2 text-xs text-gray-500">Berapa lama durasi PKL yang anda jalani saat sekolah?</p><select wire:model.defer="g2DurasiPkl"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="kurang_6_bulan">Kurang dari 6 bulan</option>
                        <option value="6_bulan">6 bulan</option>
                        <option value="lebih_6_bulan">Lebih dari 6 bulan</option>
                    </select>
                    @error('g2DurasiPkl')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">G3. Kualitas PKL</label>
                    <p class="mb-2 text-xs text-gray-500">Bagaimana penilaian anda terhadap kualitas pelaksanaan PKL?</p><select wire:model.defer="g3KualitasPkl"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="sangat_tidak_baik">Sangat tidak baik</option>
                        <option value="tidak_baik">Tidak baik</option>
                        <option value="baik">Baik</option>
                        <option value="sangat_baik">Sangat baik</option>
                    </select>
                    @error('g3KualitasPkl')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="mb-1 block text-sm font-medium text-gray-700">G4. Kesesuaian PKL</label>
                    <p class="mb-2 text-xs text-gray-500">Seberapa sesuai materi PKL dengan kompetensi keahlian anda?</p><select wire:model.defer="g4KesesuaianPkl"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih</option>
                        <option value="sangat_tidak_baik">Sangat tidak baik</option>
                        <option value="tidak_baik">Tidak baik</option>
                        <option value="baik">Baik</option>
                        <option value="sangat_baik">Sangat baik</option>
                    </select>
                    @error('g4KesesuaianPkl')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
