<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tracer Study Alumni - SMKN 3 Pontianak</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-linear-to-b from-blue-50 to-white font-sans antialiased">
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
        $kampusFavoritTotal = (int) $kampusFavorit->sum('total');

        $formatPercent = fn(float|int $value): string => number_format((float) $value, 1, ',', '.');
    @endphp

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">
                        S3
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">SMKN 3 Pontianak</h1>
                        <p class="text-xs text-gray-600">Tracer Study Alumni</p>
                    </div>
                </div>
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/home') }}"
                                class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">Login</a>
                            @if (Route::has('alumni.claim'))
                                <a href="{{ route('alumni.claim') }}"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Klaim
                                    Alumni</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-linear-to-br from-blue-600 to-blue-800 text-white">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC41IiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20">
        </div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Tracer Study Alumni SMKN 3 Pontianak</h2>
                <p class="text-lg md:text-xl text-blue-100 mb-8">Sistem pelacakan alumni untuk mengetahui perkembangan
                    karir dan kesuksesan lulusan SMKN 3 Pontianak</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('alumni.claim') }}"
                        class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg">Daftar
                        Sekarang</a>
                    <a href="#statistik"
                        class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">Lihat
                        Statistik</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="statistik" class="py-12 -mt-16 relative z-10">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 text-blue-600 w-12 h-12 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($totalAlumni) }}</h3>
                    <p class="text-gray-600 font-medium">Total Alumni Terdaftar</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 text-green-600 w-12 h-12 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $formatPercent($persenBekerja) }}%</h3>
                    <p class="text-gray-600 font-medium">Alumni Bekerja</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="bg-purple-100 text-purple-600 w-12 h-12 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $formatPercent($persenStudi) }}%</h3>
                    <p class="text-gray-600 font-medium">Melanjutkan Kuliah</p>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="bg-yellow-100 text-yellow-600 w-12 h-12 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">{{ $formatPercent($persenWirausaha) }}%</h3>
                    <p class="text-gray-600 font-medium">Alumni Berwirausaha</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Detailed Statistics Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">Statistik Ringkas Publik</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-linear-to-br from-blue-50 to-white rounded-xl shadow-lg p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Tren Alumni per Tahun Lulus</h3>
                    @php
                        $maxAlumniPerTahun = $alumniPerTahun->max('total') ?? 0;
                    @endphp
                    <div class="space-y-4">
                        @forelse ($alumniPerTahun as $row)
                            @php
                                $barWidth =
                                    $maxAlumniPerTahun > 0 ? round(($row->total / $maxAlumniPerTahun) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-700 font-medium">{{ $row->tahun_lulus }}</span>
                                    <span class="text-gray-900 font-bold">{{ number_format($row->total) }}
                                        ({{ $formatPercent($alumniPerTahunTotal > 0 ? round(($row->total / $alumniPerTahunTotal) * 100, 1) : 0) }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $barWidth }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Belum ada data tahun lulus.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-linear-to-br from-green-50 to-white rounded-xl shadow-lg p-6 md:p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Statistik Instansi, Gaji, dan Keselarasan</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700 font-medium">Respon Tracer Submitted</span>
                                <span
                                    class="text-gray-900 font-bold">{{ number_format($totalSubmittedTracer) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full"
                                    style="width: {{ $totalAlumni > 0 ? round(($jumlahPengisiTracer / $totalAlumni) * 100, 1) : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-green-100 bg-white px-3 py-3">
                            <p class="text-sm text-gray-600">Average Gaji (Estimasi)</p>
                            <p class="mt-1 text-xl font-bold text-gray-900">
                                {{ $averageGaji ? 'Rp ' . number_format($averageGaji, 0, ',', '.') : '-' }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-gray-700">Jenis Instansi Teratas</p>
                            @forelse ($jenisInstansiStats as $instansi)
                                <div
                                    class="flex items-center justify-between rounded-lg border border-gray-100 bg-white px-3 py-2">
                                    <p class="text-sm text-gray-700 truncate pr-3">
                                        {{ $instansiLabels[$instansi->c8_jenis_instansi] ?? str_replace('_', ' ', str($instansi->c8_jenis_instansi)->title()) }}
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ number_format($instansi->total) }}
                                        ({{ $formatPercent($instansiTotal > 0 ? round(($instansi->total / $instansiTotal) * 100, 1) : 0) }}%)
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600">Belum ada data jenis instansi.</p>
                            @endforelse
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2">
                                <p class="mb-2 text-xs font-semibold text-gray-600">Keselarasan Pekerjaan</p>
                                <div class="space-y-1">
                                    @forelse ($keselarasanPekerjaan as $row)
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="text-gray-700">{{ $keselarasanLabels[$row->c14_kesesuaian_pekerjaan] ?? str_replace('_', ' ', str($row->c14_kesesuaian_pekerjaan)->title()) }}</span>
                                            <span class="font-semibold text-gray-900">{{ number_format($row->total) }}
                                                ({{ $formatPercent($keselarasanPekerjaanTotal > 0 ? round(($row->total / $keselarasanPekerjaanTotal) * 100, 1) : 0) }}%)
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-500">Belum ada data.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="rounded-lg border border-gray-100 bg-white px-3 py-2">
                                <p class="mb-2 text-xs font-semibold text-gray-600">Keselarasan Studi</p>
                                <div class="space-y-1">
                                    @forelse ($keselarasanStudi as $row)
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="text-gray-700">{{ $keselarasanLabels[$row->d5_kesesuaian_studi] ?? str_replace('_', ' ', str($row->d5_kesesuaian_studi)->title()) }}</span>
                                            <span class="font-semibold text-gray-900">{{ number_format($row->total) }}
                                                ({{ $formatPercent($keselarasanStudiTotal > 0 ? round(($row->total / $keselarasanStudiTotal) * 100, 1) : 0) }}%)
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-gray-500">Belum ada data.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-gray-700">Kampus Favorit</p>
                            @forelse ($kampusFavorit as $kampus)
                                <div
                                    class="flex items-center justify-between rounded-lg border border-gray-100 bg-white px-3 py-2">
                                    <p class="text-sm text-gray-700 truncate pr-3">{{ $kampus->d3_nama_pt }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ number_format($kampus->total) }}
                                        ({{ $formatPercent($kampusFavoritTotal > 0 ? round(($kampus->total / $kampusFavoritTotal) * 100, 1) : 0) }}%)
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-gray-600">Belum ada data kampus dari isian tracer.</p>
                            @endforelse
                        </div>

                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700 font-medium">Pengisi Tracer (Alumni Unik)</span>
                                <span class="text-gray-900 font-bold">{{ number_format($jumlahPengisiTracer) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-green-100 rounded-lg">
                        <p class="text-sm text-green-800"><strong>Insight:</strong> Statistik ini bersifat agregat dan
                            diperbarui otomatis dari data tracer alumni.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-4">Mengapa Mengisi Tracer Study?
            </h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">Data yang Anda berikan sangat membantu sekolah
                untuk meningkatkan kualitas pendidikan</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-xl transition">
                    <div class="bg-blue-100 text-blue-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Evaluasi Kurikulum</h3>
                    <p class="text-gray-600">Membantu sekolah mengevaluasi dan menyempurnakan kurikulum agar sesuai
                        dengan kebutuhan industri.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-xl transition">
                    <div
                        class="bg-green-100 text-green-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jaringan Alumni</h3>
                    <p class="text-gray-600">Terhubung dengan sesama alumni SMKN 3 untuk berbagi pengalaman dan peluang
                        karir.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-xl transition">
                    <div
                        class="bg-purple-100 text-purple-600 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Peluang Kerja</h3>
                    <p class="text-gray-600">Dapatkan informasi lowongan pekerjaan yang sesuai dengan keahlian dan
                        minat Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-linear-to-r from-blue-600 to-blue-800 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Bergabunglah dengan Tracer Study Kami</h2>
            <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">Luangkan waktu 5-10 menit untuk mengisi data Anda
                dan bantu SMKN 3 Pontianak menjadi lebih baik</p>
            <a href="{{ route('alumni.claim') }}"
                class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition shadow-lg">Mulai
                Isi Data</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">SMKN 3 Pontianak</h3>
                    <p class="text-gray-400 text-sm">Sistem Tracer Study untuk melacak perkembangan karir alumni dan
                        meningkatkan kualitas pendidikan.</p>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm">
                        <li>Jl. A. Yani, Pontianak</li>
                        <li>Email: info@smkn3ptk.sch.id</li>
                        <li>Telp: (0561) 123456</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Link Cepat</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} SMKN 3 Pontianak. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
