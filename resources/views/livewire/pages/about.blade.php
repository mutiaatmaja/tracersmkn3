<?php

use Livewire\Component;

/**
 * Livewire Component untuk halaman Tentang Aplikasi
 *
 * Menampilkan informasi aplikasi, fitur, dan keterangan developer
 */
new class extends Component {
    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.pages.about');
    }
};
?>

<div class="space-y-8">
    {{-- ========== HEADER ========== --}}
    <div class="text-center">
        <div class="mb-4">
            <div
                class="inline-block bg-blue-600 text-white w-16 h-16 rounded-2xl flex items-center justify-center font-bold text-2xl shadow-lg">
                S3
            </div>
        </div>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Tracer Study Alumni</h1>
        <p class="text-xl text-gray-600">SMKN 3 Pontianak</p>
    </div>

    {{-- ========== DESKRIPSI ========== --}}
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">📋 Tentang Aplikasi</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
            Tracer Study Alumni adalah sistem manajemen terukur untuk melacak, menganalisis, dan mengelola jejak lulusan
            SMKN 3 Pontianak.
            Aplikasi ini dirancang untuk membantu sekolah dalam:
        </p>
        <ul class="space-y-3 mb-6">
            <li class="flex items-start gap-3">
                <span class="text-blue-600 font-bold text-lg mt-1">✓</span>
                <span class="text-gray-700"><strong>Melacak Status Alumni</strong> - memantau status pekerjaan,
                    pendidikan, dan kehidupan alumni secara real-time</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-blue-600 font-bold text-lg mt-1">✓</span>
                <span class="text-gray-700"><strong>Analisis Data Alumni</strong> - mengidentifikasi tren, pola, dan
                    insights berharga dari data lulusan</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-blue-600 font-bold text-lg mt-1">✓</span>
                <span class="text-gray-700"><strong>Manajemen Kompetensi</strong> - mengelola program keahlian dan
                    hubungannya dengan kesuksesan alumni</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-blue-600 font-bold text-lg mt-1">✓</span>
                <span class="text-gray-700"><strong>Pengaturan Tracer</strong> - mengkonfigurasi jadwal, durasi, dan
                    parameter tracer study</span>
            </li>
            <li class="flex items-start gap-3">
                <span class="text-blue-600 font-bold text-lg mt-1">✓</span>
                <span class="text-gray-700"><strong>Laporan Terukur</strong> - menghasilkan laporan mendalam dan
                    visualisasi data yang informatif</span>
            </li>
        </ul>
    </div>

    {{-- ========== FITUR ========== --}}
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 Fitur Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="font-bold text-blue-900 mb-2">📊 Dashboard Analytics</h3>
                <p class="text-sm text-blue-800">Visualisasi data alumni dengan grafik dan statistik real-time</p>
            </div>
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <h3 class="font-bold text-green-900 mb-2">👥 Manajemen Alumni</h3>
                <p class="text-sm text-green-800">CRUD lengkap untuk data alumni dan profil pribadi</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                <h3 class="font-bold text-purple-900 mb-2">📝 Tracer Survey</h3>
                <p class="text-sm text-purple-800">Form terstruktur dengan 8 seksi untuk pengumpulan data alumni</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                <h3 class="font-bold text-orange-900 mb-2">📋 Laporan PDF</h3>
                <p class="text-sm text-orange-800">Export laporan dan data alumni ke format PDF</p>
            </div>
            <div class="p-4 bg-pink-50 rounded-lg border border-pink-200">
                <h3 class="font-bold text-pink-900 mb-2">🔐 Role-Based Access</h3>
                <p class="text-sm text-pink-800">Kontrol akses berbasis peran untuk keamanan data</p>
            </div>
            <div class="p-4 bg-teal-50 rounded-lg border border-teal-200">
                <h3 class="font-bold text-teal-900 mb-2">⚙️ Pengaturan Dinamis</h3>
                <p class="text-sm text-teal-800">Konfigurasi aplikasi tanpa perlu akses database</p>
            </div>
        </div>
    </div>

    {{-- ========== TEKNOLOGI ========== --}}
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🛠️ Teknologi & Stack</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div class="p-3 bg-orange-50 rounded-lg border border-orange-200 text-center">
                <p class="font-semibold text-orange-900">PHP 8.5.2</p>
                <p class="text-xs text-orange-700">Backend</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg border border-red-200 text-center">
                <p class="font-semibold text-red-900">Laravel 12</p>
                <p class="text-xs text-red-700">Framework</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg border border-purple-200 text-center">
                <p class="font-semibold text-purple-900">Livewire 4</p>
                <p class="text-xs text-purple-700">Reaktif UI</p>
            </div>
            <div class="p-3 bg-cyan-50 rounded-lg border border-cyan-200 text-center">
                <p class="font-semibold text-cyan-900">Tailwind CSS 4</p>
                <p class="text-xs text-cyan-700">Styling</p>
            </div>
            <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-center">
                <p class="font-semibold text-emerald-900">SQLite</p>
                <p class="text-xs text-emerald-700">Database</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200 text-center">
                <p class="font-semibold text-indigo-900">Spatie PDF</p>
                <p class="text-xs text-indigo-700">PDF Export</p>
            </div>
        </div>
    </div>

    {{-- ========== DEVELOPER ========== --}}
    <div class="max-w-2xl mx-auto bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-lg p-8 text-white">
        <div class="text-center">
            <h2 class="text-2xl font-bold mb-2">💻 Dikembangkan Oleh</h2>
            <p class="text-3xl font-bold mb-1">Mutia Atmaja</p>
            <p class="text-blue-100">Software Developer & System Designer</p>
        </div>
    </div>

    {{-- ========== COPYRIGHT ========== --}}
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
            <p class="text-gray-600 mb-2">
                <strong>© 2026 Tracer Study Alumni System</strong>
            </p>
            <p class="text-sm text-gray-500 mb-3">
                SMKN 3 Pontianak | Semua Hak Dilindungi
            </p>
            <p class="text-xs text-gray-400">
                Developed with ❤️ for improving alumni tracking and analysis in vocational education.
            </p>
            <p class="text-xs text-gray-400 mt-3">
                Version 1.0.0 • February 19, 2026
            </p>
        </div>
    </div>

</div>
