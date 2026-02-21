<?php

use App\Exports\AlumnisDummyExport;
use App\Exports\AlumnisTemplateExport;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

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
});

// Admin Routes - Livewire Pages
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::livewire('/admin/alumni', 'pages.alumnis')->name('alumnis');
    Route::get('/admin/alumni/template/download', function () {
        return Excel::download(new AlumnisTemplateExport, 'template_import_alumni.xlsx');
    })->name('alumnis.template.download');
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
