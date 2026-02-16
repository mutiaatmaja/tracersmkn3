<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tracer Study Alumni - SMKN 3 Pontianak')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles()
</head>

<body class="bg-gradient-to-b from-blue-50 to-white font-sans antialiased text-gray-900">

    <div id="app" class="min-h-screen flex flex-col">

        {{-- ================= NAVBAR ================= --}}
        <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    {{-- Logo --}}
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <div
                            class="bg-blue-600 text-white w-10 h-10 rounded-xl flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition">
                            S3
                        </div>
                        <div class="leading-tight">
                            <p class="text-lg font-bold text-gray-900">SMKN 3 Pontianak</p>
                            <p class="text-xs text-gray-500">Tracer Study Alumni</p>
                        </div>
                    </a>

                    {{-- Auth Menu --}}
                    <div class="flex items-center space-x-4">
                        @guest
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                    class="text-gray-700 hover:text-blue-600 font-medium transition">
                                    Login
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">
                                    Register
                                </a>
                            @endif
                        @else
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600 font-medium">
                                    {{ Auth::user()->name }}
                                </span>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-blue-600 font-medium transition">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @endguest
                    </div>

                </div>
            </div>
        </nav>

        {{-- ================= ADMIN MENU ================= --}}
        @auth
            @if (Auth::user()->hasRole('admin'))
                <div class="bg-white border-b border-gray-100">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-2 text-sm">

                            {{-- Dashboard --}}
                            <a href="{{ route('home') }}" wire:navigate
                                class="{{ request()->is('home*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Dashboard
                            </a>

                            {{-- Data Alumni --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/alumni*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Data Alumni
                            </a>

                            {{-- Tracer Survey --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/tracer*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Tracer Survey
                            </a>

                            {{-- Laporan --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/laporan*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Laporan
                            </a>

                            {{-- Lowongan --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/lowongan*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Lowongan
                            </a>

                            {{-- Event --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/event*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Event
                            </a>

                            {{-- Pengguna --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/pengguna*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Pengguna
                            </a>

                            {{-- Pengaturan --}}
                            <a href="#" wire:navigate
                                class="{{ request()->is('admin/pengaturan*') ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}
               text-center px-3 py-2 rounded-lg font-medium transition-all duration-200">

                                Pengaturan
                            </a>

                        </div>

                    </div>
                </div>
            @endif
        @endauth


        {{-- ================= CONTENT ================= --}}
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Flash Message --}}
                @if (session('success'))
                    <div class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
                        Terjadi kesalahan pada input.
                    </div>
                @endif

                @yield('content')

                @isset($slot)
                    {{ $slot }}
                @endisset

            </div>
        </main>

    </div>

    @livewireScripts()
</body>

</html>
