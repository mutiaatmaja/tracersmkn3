@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Alumni</h1>
                    <p class="text-gray-600">Selamat datang kembali. Lengkapi data tracer study Anda di sini.</p>
                </div>
                <a href="#"
                    class="inline-flex items-center justify-center bg-blue-600 text-white px-5 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Isi
                    Tracer Study</a>
            </div>
            @if (session('status'))
                <div class="mt-4 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <p class="text-sm text-gray-500">Status Tracer Study</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">Belum Lengkap</p>
                <p class="text-xs text-gray-500 mt-2">Lengkapi agar data Anda tervalidasi.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <p class="text-sm text-gray-500">Peluang Kerja Baru</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">12</p>
                <p class="text-xs text-gray-500 mt-2">Update minggu ini.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <p class="text-sm text-gray-500">Event Alumni</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">3</p>
                <p class="text-xs text-gray-500 mt-2">Akan datang bulan ini.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <p class="text-sm text-gray-500">Profil Anda</p>
                <p class="text-2xl font-bold text-gray-900 mt-2">80%</p>
                <p class="text-xs text-gray-500 mt-2">Lengkapi agar mencapai 100%.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mt-6">
            <h2 class="text-xl font-bold text-gray-900
            mb-6">Menu Alumni</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="#" class="group bg-blue-50 rounded-xl p-5 hover:bg-blue-100 transition">
                    <h3 class="text-lg font-semibold text-blue-700">Profil Alumni</h3>
                    <p class="text-sm text-blue-600 mt-1">Perbarui biodata dan informasi karir.</p>
                </a>
                <a href="#" class="group bg-green-50 rounded-xl p-5 hover:bg-green-100 transition">
                    <h3 class="text-lg font-semibold text-green-700">Tracer Study</h3>
                    <p class="text-sm text-green-600 mt-1">Isi kuisioner tracer study terbaru.</p>
                </a>
                <a href="#" class="group bg-purple-50 rounded-xl p-5 hover:bg-purple-100 transition">
                    <h3 class="text-lg font-semibold text-purple-700">Lowongan Kerja</h3>
                    <p class="text-sm text-purple-600 mt-1">Lihat peluang kerja dari mitra.</p>
                </a>
                <a href="#" class="group bg-yellow-50 rounded-xl p-5 hover:bg-yellow-100 transition">
                    <h3 class="text-lg font-semibold text-yellow-700">Event Alumni</h3>
                    <p class="text-sm text-yellow-600 mt-1">Ikuti kegiatan dan reuni alumni.</p>
                </a>
                <a href="#" class="group bg-rose-50 rounded-xl p-5 hover:bg-rose-100 transition">
                    <h3 class="text-lg font-semibold text-rose-700">Pengumuman</h3>
                    <p class="text-sm text-rose-600 mt-1">Info terbaru dari sekolah.</p>
                </a>
                <a href="#" class="group bg-slate-50 rounded-xl p-5 hover:bg-slate-100 transition">
                    <h3 class="text-lg font-semibold text-slate-700">Bantuan</h3>
                    <p class="text-sm text-slate-600 mt-1">Panduan pengisian dan FAQ.</p>
                </a>
            </div>
        </div>
    </div>
@endsection
