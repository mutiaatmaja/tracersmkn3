@extends('layouts.app')

@section('content')
    @php
        $instansiLabels = [
            'instansi_pemerintah' => 'Instansi Pemerintah',
            'lembaga_internasional' => 'Lembaga Internasional',
            'lembaga_non_profit' => 'Lembaga Non-profit',
            'perusahaan_swasta_bumn_bumd' => 'Perusahaan Swasta / BUMN / BUMD',
            'koperasi' => 'Koperasi',
            'usaha_perorangan' => 'Usaha Perorangan',
            'rumah_tangga' => 'Rumah Tangga',
        ];

        $keselarasanLabels = [
            'sangat_tidak_selaras' => 'Sangat Tidak Selaras',
            'tidak_selaras' => 'Tidak Selaras',
            'selaras' => 'Selaras',
            'sangat_selaras' => 'Sangat Selaras',
        ];

        $alumniPerTahunTotal = (int) $alumniPerTahun->sum('total');
        $instansiTotal = (int) $jenisInstansiStats->sum('total');
        $keselarasanPekerjaanTotal = (int) $keselarasanPekerjaan->sum('total');
        $keselarasanStudiTotal = (int) $keselarasanStudi->sum('total');

        $formatPercent = fn(float|int $value): string => number_format((float) $value, 1, ',', '.');
    @endphp

    @auth
        <div class="space-y-8">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        @if (auth()->user()->hasRole('alumni'))
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Alumni</h1>
                            <p class="text-gray-600">Ringkasan statistik tracer study sekolah.</p>
                        @else
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Admin/Staff</h1>
                            <p class="text-gray-600">Ringkasan statistik pengelolaan tracer study alumni.</p>
                        @endif
                    </div>

                    @if (auth()->user()->hasRole('alumni'))
                        <a href="{{ route('tracer.study') }}"
                            class="inline-flex items-center justify-center bg-blue-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Isi Tracer Study
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Total Alumni</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($totalAlumni) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Data alumni terdaftar di sistem.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Alumni Mengisi Tracer</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($jumlahPengisiTracer) }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $formatPercent($persentasePengisiTracer) }}% dari total
                        alumni.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Periode Tracer Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $latestPeriodeTracer ?? '-' }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ number_format($jumlahPengisiTracerPeriodeAktif) }} respon
                        submitted.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Studi Lanjut</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($jumlahStudiLanjut) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Dari tracer berstatus submitted.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Keberkerjaan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($jumlahBekerja) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Alumni yang menyatakan bekerja.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Kewirausahaan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($jumlahWirausaha) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Responden dengan bentuk kerja wirausaha.</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <p class="text-sm text-gray-500">Tren Alumni per Tahun Lulus</p>
                    <div class="mt-2 space-y-1">
                        @forelse ($alumniPerTahun as $row)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $row->tahun_lulus }}</span>
                                <span class="font-semibold text-gray-900">{{ number_format($row->total) }}
                                    ({{ $formatPercent($alumniPerTahunTotal > 0 ? round(($row->total / $alumniPerTahunTotal) * 100, 1) : 0) }}%)
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada data tahun lulus.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900">Mini Chart Tren Alumni</h2>
                    <p class="text-sm text-gray-500 mt-1">Visual sederhana jumlah alumni per tahun lulus.</p>

                    @php
                        $maxAlumniPerTahun = $alumniPerTahun->max('total') ?? 0;
                    @endphp

                    <div class="mt-4 space-y-3">
                        @forelse ($alumniPerTahun as $row)
                            @php
                                $barWidth =
                                    $maxAlumniPerTahun > 0 ? round(($row->total / $maxAlumniPerTahun) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="mb-1 flex items-center justify-between text-sm">
                                    <span class="text-gray-700">{{ $row->tahun_lulus }}</span>
                                    <span class="font-semibold text-gray-900">{{ number_format($row->total) }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-blue-600" style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada data untuk divisualisasikan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900">Statistik Jenis Instansi</h2>
                    <p class="text-sm text-gray-500 mt-1">Distribusi jenis instansi tempat alumni bekerja.</p>

                    <div class="mt-4 space-y-2">
                        @forelse ($jenisInstansiStats as $instansi)
                            <div
                                class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                <p class="text-sm text-gray-700 truncate pr-3">
                                    {{ $instansiLabels[$instansi->c8_jenis_instansi] ?? str_replace('_', ' ', str($instansi->c8_jenis_instansi)->title()) }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($instansi->total) }}
                                    ({{ $formatPercent($instansiTotal > 0 ? round(($instansi->total / $instansiTotal) * 100, 1) : 0) }}%)
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada data jenis instansi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-900">Average Gaji</h2>
                    <p class="text-sm text-gray-500 mt-1">Estimasi dari rentang penghasilan tracer submitted.</p>
                    <p class="mt-4 text-3xl font-bold text-gray-900">
                        {{ $averageGaji ? 'Rp ' . number_format($averageGaji, 0, ',', '.') : '-' }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                    <h2 class="text-lg font-bold text-gray-900">Keselarasan Bidang</h2>
                    <p class="text-sm text-gray-500 mt-1">Perbandingan keselarasan pekerjaan dan studi lanjut.</p>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2 text-sm font-semibold text-gray-700">Keselarasan Pekerjaan</p>
                            <div class="space-y-2">
                                @forelse ($keselarasanPekerjaan as $row)
                                    <div
                                        class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                        <span
                                            class="text-sm text-gray-700">{{ $keselarasanLabels[$row->c14_kesesuaian_pekerjaan] ?? str_replace('_', ' ', str($row->c14_kesesuaian_pekerjaan)->title()) }}</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($row->total) }}
                                            ({{ $formatPercent($keselarasanPekerjaanTotal > 0 ? round(($row->total / $keselarasanPekerjaanTotal) * 100, 1) : 0) }}%)
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada data keselarasan pekerjaan.</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <p class="mb-2 text-sm font-semibold text-gray-700">Keselarasan Studi</p>
                            <div class="space-y-2">
                                @forelse ($keselarasanStudi as $row)
                                    <div
                                        class="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                        <span
                                            class="text-sm text-gray-700">{{ $keselarasanLabels[$row->d5_kesesuaian_studi] ?? str_replace('_', ' ', str($row->d5_kesesuaian_studi)->title()) }}</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($row->total) }}
                                            ({{ $formatPercent($keselarasanStudiTotal > 0 ? round(($row->total / $keselarasanStudiTotal) * 100, 1) : 0) }}%)
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">Belum ada data keselarasan studi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2">Silakan login untuk melihat dashboard sesuai peran Anda.</p>
        </div>
    @endauth
@endsection
