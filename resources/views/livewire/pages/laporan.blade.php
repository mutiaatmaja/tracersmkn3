<?php

use App\Models\Alumni;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Livewire Component untuk halaman Laporan
 */
new class extends Component {
    public string $filterTahunLulus = '';

    protected function filteredAlumniQuery(): Builder
    {
        $query = Alumni::query();

        if ($this->filterTahunLulus !== '') {
            $query->where('tahun_lulus', (int) $this->filterTahunLulus);
        }

        return $query;
    }

    #[Computed]
    public function daftarTahunLulus()
    {
        return Alumni::query()->whereNotNull('tahun_lulus')->select('tahun_lulus')->distinct()->orderByDesc('tahun_lulus')->pluck('tahun_lulus');
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
            <button type="button"
                class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                Laporan Alumni
            </button>
            <button type="button"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-500">
                Laporan Tracer Study
            </button>
            <button type="button"
                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-500">
                Laporan Penempatan Kerja
            </button>
        </div>
    </div>

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
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($this->statistikKlaim['total']) }}</p>
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
                                <td colspan="2" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data
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
                                <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data
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
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $this->statistikUmur['rata_rata'] }} tahun
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    @foreach ($this->statistikUmur['bucket'] as $label => $jumlah)
                        <div class="rounded-lg border border-gray-200 bg-white p-3"
                            wire:key="bucket-{{ $label }}">
                            <p class="text-xs text-gray-500">{{ $label }}</p>
                            <p class="mt-1 text-base font-semibold text-gray-900">{{ number_format($jumlah) }} alumni
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    Data umur terisi: <span
                        class="font-semibold">{{ number_format($this->statistikUmur['total_terdata']) }}</span>
                    · Tidak terisi:
                    <span class="font-semibold">{{ number_format($this->statistikUmur['total_tidak_terdata']) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
