<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/template', function () {
    return view('template');
});

// Tentang Aplikasi
Route::livewire('/tentang', 'pages.about')->name('about');

// Profil
Route::middleware(['auth'])->group(function () {
    Route::livewire('/profil', 'pages.profile')->name('profile');
});

// Admin Routes - Livewire Pages
Route::middleware(['auth', 'role:admin'])->group(function () {
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
