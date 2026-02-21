@extends('layouts.app')

@section('content')
    @auth
        @if (auth()->user()->hasRole('alumni'))
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Alumni</h1>
                            <p class="text-gray-600">Ringkasan statistik pribadi alumni Anda.</p>
                        </div>
                        <a href="#"
                            class="inline-flex items-center justify-center bg-blue-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Isi
                            Tracer Study</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Status Tracer Study</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">Sudah Isi</p>
                        <p class="text-xs text-gray-500 mt-2">Periode terakhir: Maret 2026</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Profil Alumni</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">92%</p>
                        <p class="text-xs text-gray-500 mt-2">Lengkapi 1 data lagi untuk 100%.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Peluang Kerja Baru</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">8</p>
                        <p class="text-xs text-gray-500 mt-2">Sesuai kompetensi Anda.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Event Alumni</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">2</p>
                        <p class="text-xs text-gray-500 mt-2">Terjadwal bulan ini.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Admin/Staff</h1>
                    <p class="text-gray-600">Ringkasan statistik pengelolaan tracer study (hardcode sementara).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Total Alumni</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">1,284</p>
                        <p class="text-xs text-gray-500 mt-2">Data aktif terdaftar.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Respon Tracer</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">67%</p>
                        <p class="text-xs text-gray-500 mt-2">Periode berjalan.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Lowongan Aktif</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">24</p>
                        <p class="text-xs text-gray-500 mt-2">Dari mitra industri.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <p class="text-sm text-gray-500">Event Mendatang</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">5</p>
                        <p class="text-xs text-gray-500 mt-2">Agenda 30 hari ke depan.</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-2">Silakan login untuk melihat dashboard sesuai peran Anda.</p>
        </div>
    @endauth
@endsection
