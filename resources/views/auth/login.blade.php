@extends('layouts.app')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">Masuk</h1>
                    <p class="text-sm text-gray-600">Silakan login untuk melanjutkan</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email" autofocus
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('password')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center text-sm text-gray-600">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ old('remember') ? 'checked' : '' }}>
                            <span class="ml-2">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-700" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Masuk
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="text-center text-sm text-gray-600 mt-6">
                        Belum punya akun?
                        <a class="text-blue-600 hover:text-blue-700 font-medium" href="{{ route('register') }}">Daftar</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
