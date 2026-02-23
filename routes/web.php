<?php

use App\Exports\AlumnisDummyExport;
use App\Http\Controllers\LaporanController;
use App\Models\Alumni;
use App\Models\TracerSubmission;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    $totalAlumni = Alumni::query()->count();

    $submittedTracerQuery = TracerSubmission::query()->where('status', 'submitted');
    $totalSubmittedTracer = (clone $submittedTracerQuery)->count();
    $jumlahPengisiTracer = (clone $submittedTracerQuery)->distinct('alumni_id')->count('alumni_id');

    $jumlahBekerja = (clone $submittedTracerQuery)->where('b2_bekerja', true)->count();
    $jumlahStudi = (clone $submittedTracerQuery)->where('b1_studi_lanjut', true)->count();
    $jumlahWirausaha = (clone $submittedTracerQuery)
        ->whereIn('b3_bentuk_pekerjaan', [
            'wirausaha_tanpa_pekerja',
            'wirausaha_pekerja_tidak_dibayar',
            'wirausaha_pekerja_dibayar',
        ])
        ->count();

    $persenBekerja = $totalSubmittedTracer > 0 ? round(($jumlahBekerja / $totalSubmittedTracer) * 100, 1) : 0;
    $persenStudi = $totalSubmittedTracer > 0 ? round(($jumlahStudi / $totalSubmittedTracer) * 100, 1) : 0;
    $persenWirausaha = $totalSubmittedTracer > 0 ? round(($jumlahWirausaha / $totalSubmittedTracer) * 100, 1) : 0;

    $jenisInstansiStats = (clone $submittedTracerQuery)
        ->selectRaw('c8_jenis_instansi, COUNT(*) as total')
        ->whereNotNull('c8_jenis_instansi')
        ->where('c8_jenis_instansi', '!=', '')
        ->groupBy('c8_jenis_instansi')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    $salaryDistribution = (clone $submittedTracerQuery)
        ->selectRaw('c10_penghasilan_bulanan, COUNT(*) as total')
        ->whereNotNull('c10_penghasilan_bulanan')
        ->where('c10_penghasilan_bulanan', '!=', '')
        ->groupBy('c10_penghasilan_bulanan')
        ->get();

    $salaryMap = [
        'kurang_3_juta' => 2500000,
        '3_5_juta' => 4000000,
        'lebih_5_juta' => 6000000,
    ];

    $jumlahResponBergaji = $salaryDistribution->sum(function ($row) use ($salaryMap) {
        return isset($salaryMap[$row->c10_penghasilan_bulanan]) ? (int) $row->total : 0;
    });

    $totalAkumulasiGaji = $salaryDistribution->sum(function ($row) use ($salaryMap) {
        return ($salaryMap[$row->c10_penghasilan_bulanan] ?? 0) * (int) $row->total;
    });

    $averageGaji = $jumlahResponBergaji > 0
        ? (int) round($totalAkumulasiGaji / $jumlahResponBergaji)
        : null;

    $keselarasanPekerjaan = (clone $submittedTracerQuery)
        ->selectRaw('c14_kesesuaian_pekerjaan, COUNT(*) as total')
        ->whereNotNull('c14_kesesuaian_pekerjaan')
        ->where('c14_kesesuaian_pekerjaan', '!=', '')
        ->groupBy('c14_kesesuaian_pekerjaan')
        ->orderByDesc('total')
        ->get();

    $keselarasanStudi = (clone $submittedTracerQuery)
        ->selectRaw('d5_kesesuaian_studi, COUNT(*) as total')
        ->whereNotNull('d5_kesesuaian_studi')
        ->where('d5_kesesuaian_studi', '!=', '')
        ->groupBy('d5_kesesuaian_studi')
        ->orderByDesc('total')
        ->get();

    $alumniPerTahun = Alumni::query()
        ->selectRaw('tahun_lulus, COUNT(*) as total')
        ->whereNotNull('tahun_lulus')
        ->groupBy('tahun_lulus')
        ->orderByDesc('tahun_lulus')
        ->limit(6)
        ->get();

    $kampusFavorit = (clone $submittedTracerQuery)
        ->selectRaw('d3_nama_pt, COUNT(*) as total')
        ->whereNotNull('d3_nama_pt')
        ->where('d3_nama_pt', '!=', '')
        ->groupBy('d3_nama_pt')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    return view('welcome', [
        'totalAlumni' => $totalAlumni,
        'jumlahPengisiTracer' => $jumlahPengisiTracer,
        'totalSubmittedTracer' => $totalSubmittedTracer,
        'jumlahBekerja' => $jumlahBekerja,
        'jumlahStudi' => $jumlahStudi,
        'jumlahWirausaha' => $jumlahWirausaha,
        'persenBekerja' => $persenBekerja,
        'persenStudi' => $persenStudi,
        'persenWirausaha' => $persenWirausaha,
        'alumniPerTahun' => $alumniPerTahun,
        'kampusFavorit' => $kampusFavorit,
        'jenisInstansiStats' => $jenisInstansiStats,
        'averageGaji' => $averageGaji,
        'keselarasanPekerjaan' => $keselarasanPekerjaan,
        'keselarasanStudi' => $keselarasanStudi,
    ]);
});

Auth::routes(['register' => false]); // Nonaktifkan registrasi default Laravel

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/template', function () {
    return view('template');
});

Route::middleware('guest')->group(function () {
    Route::livewire('/klaim-alumni', 'pages.alumni-claim')->name('alumni.claim');
});

// Tentang Aplikasi
Route::livewire('/tentang', 'pages.about')->name('about');

// Profil
Route::middleware(['auth'])->group(function () {
    Route::livewire('/profil', 'pages.profile')->name('profile');
    Route::middleware(['role:alumni'])->group(function () {
        Route::livewire('/tracer-study', 'pages.tracer-study')->name('tracer.study');
    });
});

// Admin Routes - Livewire Pages
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::livewire('/admin/alumni', 'pages.alumnis')->name('alumnis');
    Route::get('/admin/alumni/dummy/download', function () {
        return Excel::download(new AlumnisDummyExport, 'dummy_import_alumni.xlsx');
    })->name('alumnis.dummy.download');

    // Manajemen Pengguna
    Route::livewire('/admin/pengguna', 'pages.users')->name('users');
    Route::livewire('/admin/pengaturan/pengguna', 'pages.users')->name('settings.references.users');

    // Manajemen Kompetensi
    Route::livewire('/admin/kompetensi', 'pages.competencies')->name('competencies');
    Route::livewire('/admin/pengaturan/kompetensi', 'pages.competencies')->name('settings.references.competencies');

    // Manajemen Provinsi & Kota
    Route::livewire('/admin/provinsi', 'pages.provinces')->name('provinces');
    Route::livewire('/admin/pengaturan/provinsi', 'pages.provinces')->name('settings.references.provinces');
    Route::livewire('/admin/kota', 'pages.cities')->name('cities');
    Route::livewire('/admin/pengaturan/kota', 'pages.cities')->name('settings.references.cities');
    Route::livewire('/admin/universitas', 'pages.universities')->name('universities');
    Route::livewire('/admin/pengaturan/perguruan-tinggi', 'pages.universities')->name('settings.references.universities');

    // Laporan
    Route::livewire('/admin/laporan', 'pages.laporan')->name('laporan');
    Route::get('/admin/laporan/alumni/pdf', [LaporanController::class, 'alumniPdf'])->name('laporan.alumni.pdf');
    Route::get('/admin/laporan/tracer/pdf', [LaporanController::class, 'tracerPdf'])->name('laporan.tracer.pdf');

    // Lowongan
    Route::livewire('/admin/lowongan', 'pages.lowongan')->name('lowongan');

    // Event
    Route::livewire('/admin/event', 'pages.event')->name('event');

    // Pengaturan Aplikasi
    Route::livewire('/admin/pengaturan', 'pages.settings')->name('settings');
});

// Beta Testing Routes
Route::get('/beta/cetak/dompdf', [App\Http\Controllers\BetaController::class, 'betaCetakDompdf']);
Route::get('/beta/cetak/cloudflare', [App\Http\Controllers\BetaController::class, 'betaCetakCloudflare']);
