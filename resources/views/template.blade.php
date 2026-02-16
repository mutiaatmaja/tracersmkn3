@extends('layouts.app')

@section('content')
    @php
        $stats = [
            'totalAlumni' => 1280,
            'bekerja' => 860,
            'kuliah' => 240,
            'belumKerja' => 180,
        ];

        $alumni = [
            (object) [
                'nama' => 'Andi Saputra',
                'jurusan' => 'RPL',
                'tahun_lulus' => 2023,
                'status' => 'bekerja',
            ],
            (object) [
                'nama' => 'Siti Rahma',
                'jurusan' => 'AKL',
                'tahun_lulus' => 2022,
                'status' => 'kuliah',
            ],
            (object) [
                'nama' => 'Budi Hartono',
                'jurusan' => 'TKJ',
                'tahun_lulus' => 2023,
                'status' => 'belum bekerja',
            ],
            (object) [
                'nama' => 'Dewi Lestari',
                'jurusan' => 'BDP',
                'tahun_lulus' => 2021,
                'status' => 'wirausaha',
            ],
        ];
    @endphp

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Dashboard Tracer Study</h1>
        <p class="text-gray-600">Ringkasan kondisi alumni setelah lulus.</p>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        {{-- Total Alumni --}}
        <a href="#"
            class="group bg-blue-50 rounded-2xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 hover:ring-1 hover:ring-blue-200 transition border border-blue-100">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-700 font-medium">Total Alumni</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">1280</p>
                </div>

                <div class="bg-blue-100 text-blue-600 p-6 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 14l6.16-3.422A12.083 12.083 0 0112 20.055a12.083 12.083 0 01-6.16-9.477L12 14z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 text-sm text-blue-700 font-medium group-hover:underline">
                Selengkapnya →
            </div>
        </a>


        {{-- Bekerja --}}
        <a href="#"
            class="group bg-green-50 rounded-2xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 hover:ring-1 hover:ring-green-200 transition border border-green-100">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-700 font-medium">Bekerja</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">860</p>
                </div>

                <div
                    class="bg-green-100 text-green-600 p-6 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 6V5a3 3 0 016 0v1m-9 4h12m-14 0h16v9a2 2 0 01-2 2H6a2 2 0 01-2-2v-9z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 text-sm text-green-700 font-medium group-hover:underline">
                Selengkapnya →
            </div>
        </a>


        {{-- Kuliah --}}
        <a href="#"
            class="group bg-indigo-50 rounded-2xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 hover:ring-1 hover:ring-indigo-200 transition border border-indigo-100">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-indigo-700 font-medium">Kuliah</p>
                    <p class="text-3xl font-bold text-indigo-900 mt-2">240</p>
                </div>

                <div
                    class="bg-indigo-100 text-indigo-600 p-6 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m8-6H4" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 text-sm text-indigo-700 font-medium group-hover:underline">
                Selengkapnya →
            </div>
        </a>


        {{-- Belum Bekerja --}}
        <a href="#"
            class="group bg-rose-50 rounded-2xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 hover:ring-1 hover:ring-rose-200 transition border border-rose-100">

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-rose-700 font-medium">Belum Bekerja</p>
                    <p class="text-3xl font-bold text-rose-900 mt-2">180</p>
                </div>

                <div
                    class="bg-rose-100 text-rose-600 p-6 rounded-2xl group-hover:scale-110 group-hover:rotate-3 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 100 18 9 9 0 000-18z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 text-sm text-rose-700 font-medium group-hover:underline">
                Selengkapnya →
            </div>
        </a>

    </div>



    {{-- Filter UI saja --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Filter Alumni</h2>

        <form class="grid md:grid-cols-4 gap-4">
            <input type="text" placeholder="Cari nama alumni..."
                class="rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">

            <select class="rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <option>Semua Status</option>
                <option>Bekerja</option>
                <option>Kuliah</option>
                <option>Wirausaha</option>
                <option>Belum Bekerja</option>
            </select>

            <input type="number" placeholder="Tahun lulus"
                class="rounded-lg border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500">

            <button type="button"
                class="bg-blue-600 text-white rounded-lg px-4 py-2 font-semibold hover:bg-blue-700 transition">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Data Alumni</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-3 pr-6">Nama</th>
                        <th class="py-3 pr-6">Jurusan</th>
                        <th class="py-3 pr-6">Tahun Lulus</th>
                        <th class="py-3 pr-6">Status</th>
                        <th class="py-3 pr-6">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700">
                    @foreach ($alumni as $item)
                        <tr class="border-b">
                            <td class="py-3 pr-6">{{ $item->nama }}</td>
                            <td class="py-3 pr-6">{{ $item->jurusan }}</td>
                            <td class="py-3 pr-6">{{ $item->tahun_lulus }}</td>
                            <td class="py-3 pr-6">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-3 pr-6">
                                <button class="text-blue-600 hover:underline">Detail</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
