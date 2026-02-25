<?php

use App\Models\Setting;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire Component untuk manajemen Pengaturan Aplikasi
 *
 * Menampilkan form untuk edit informasi sekolah dan konfigurasi tracer
 */
new class extends Component {
    // Informasi Sekolah
    #[Validate('required|string|max:255')]
    public string $school_name = '';

    #[Validate('required|string|max:500')]
    public string $school_address = '';

    #[Validate('required|string|max:20')]
    public string $school_phone = '';

    #[Validate('required|email')]
    public string $school_email = '';

    #[Validate('required|string|max:255')]
    public string $principal_name = '';

    #[Validate('required|string|max:20')]
    public string $principal_contact = '';

    #[Validate('nullable|url')]
    public string $website = '';

    // Konfigurasi Tracer
    #[Validate('required|in:yearly,two-yearly,monthly')]
    public string $tracer_frequency = 'yearly';

    #[Validate('required|integer|min:1|max:12')]
    public int $tracer_month = 3;

    #[Validate('required|integer|min:30|max:365')]
    public int $tracer_duration_days = 90;

    public bool $saved = false;

    /**
     * Mount component - load data setting dari database
     */
    public function mount()
    {
        $this->loadSettings();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.pages.settings');
    }

    /**
     * Load semua setting dari database
     */
    private function loadSettings()
    {
        $this->school_name = Setting::get('school_name', '');
        $this->school_address = Setting::get('school_address', '');
        $this->school_phone = Setting::get('school_phone', '');
        $this->school_email = Setting::get('school_email', '');
        $this->principal_name = Setting::get('principal_name', '');
        $this->principal_contact = Setting::get('principal_contact', '');
        $this->website = Setting::get('website', '');
        $this->tracer_frequency = Setting::get('tracer_frequency', 'yearly');
        $this->tracer_month = Setting::get('tracer_month', 3);
        $this->tracer_duration_days = Setting::get('tracer_duration_days', 90);
    }

    /**
     * Simpan semua setting ke database
     */
    public function save()
    {
        $this->validate();

        // Simpan informasi sekolah
        Setting::set('school_name', $this->school_name);
        Setting::set('school_address', $this->school_address);
        Setting::set('school_phone', $this->school_phone);
        Setting::set('school_email', $this->school_email);
        Setting::set('principal_name', $this->principal_name);
        Setting::set('principal_contact', $this->principal_contact);
        Setting::set('website', $this->website);

        // Simpan konfigurasi tracer
        Setting::set('tracer_frequency', $this->tracer_frequency);
        Setting::set('tracer_month', $this->tracer_month);
        Setting::set('tracer_duration_days', $this->tracer_duration_days);

        $this->saved = true;
        $this->dispatch('toast', message: 'Pengaturan berhasil disimpan!', type: 'success');

        // Hilang notif setelah 3 detik
        $this->dispatch('clear-saved-after-3s');
    }

    /**
     * Reset form ke setting yang sebelumnya
     */
    public function reset(...$properties): void
    {
        if (empty($properties)) {
            // Reset semua properties ke nilai database
            $this->loadSettings();
            $this->saved = false;
        } else {
            // Reset property spesifik
            parent::reset(...$properties);
        }
    }
};
?>

<div class="space-y-6">
    {{-- ========== HEADER ========== --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan Aplikasi</h1>
        <p class="text-gray-600 mt-1">Kelola informasi sekolah dan konfigurasi tracer study</p>
    </div>

    {{-- ========== NOTIFICATION ========== --}}
    @if ($saved)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <div class="text-green-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-green-800">Pengaturan berhasil disimpan!</h3>
                <p class="text-sm text-green-700">Semua perubahan telah disimpan ke database.</p>
            </div>
        </div>
    @endif

    {{-- ========== DATA REFERENSI ========== --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📍 Data Referensi</h2>
        <p class="text-gray-600 mb-4">Kelola data kompetensi, provinsi, kota/kabupaten, dan perguruan tinggi</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('settings.references.competencies') }}" wire:navigate
                class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition group">
                <div class="text-3xl">🎓</div>
                <div>
                    <h3 class="font-semibold text-amber-900 group-hover:text-amber-700">Kompetensi</h3>
                    <p class="text-sm text-amber-700">Kelola data kompetensi</p>
                </div>
            </a>

            {{-- <a href="{{ route('settings.references.provinces') }}" wire:navigate
                class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition group">
                <div class="text-3xl">🗺️</div>
                <div>
                    <h3 class="font-semibold text-blue-900 group-hover:text-blue-700">Provinsi</h3>
                    <p class="text-sm text-blue-700">Kelola data provinsi</p>
                </div>
            </a>

            <a href="{{ route('settings.references.cities') }}" wire:navigate
                class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition group">
                <div class="text-3xl">🏙️</div>
                <div>
                    <h3 class="font-semibold text-green-900 group-hover:text-green-700">Kota/Kabupaten</h3>
                    <p class="text-sm text-green-700">Kelola data kota & kabupaten</p>
                </div>
            </a>

            <a href="{{ route('settings.references.universities') }}" wire:navigate
                class="flex items-center gap-3 p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition group">
                <div class="text-3xl">🎓</div>
                <div>
                    <h3 class="font-semibold text-purple-900 group-hover:text-purple-700">Perguruan Tinggi</h3>
                    <p class="text-sm text-purple-700">Kelola data universitas</p>
                </div>
            </a> --}}
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- ========== INFORMASI SEKOLAH ========== --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">📋 Informasi Sekolah</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="school_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="SMKN 3 Pontianak">
                    @error('school_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Alamat Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alamat Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="school_address"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Jl. Jend. A. Yani, Pontianak">
                    @error('school_address')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Telepon Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Telepon Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" wire:model="school_phone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="(0561) 123456">
                    @error('school_phone')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="email" wire:model="school_email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="sekolah@example.com">
                    @error('school_email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Nama Kepala Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Kepala Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="principal_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Drs. H. Suparman, M.Pd.">
                    @error('principal_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Kontak Kepala Sekolah --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kontak Kepala Sekolah <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" wire:model="principal_contact"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="(0561) 234567">
                    @error('principal_contact')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Website Sekolah --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Website Sekolah
                    </label>
                    <input type="url" wire:model="website"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="https://smkn3pontianak.sch.id">
                    @error('website')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ========== KONFIGURASI TRACER ========== --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">⚙️ Konfigurasi Tracer Study</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Frekuensi Tracer --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Frekuensi Pengisian Tracer <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="tracer_frequency"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="yearly">Setahun Sekali</option>
                        <option value="two-yearly">Dua Tahun Sekali</option>
                        <option value="monthly">Setiap Bulan</option>
                    </select>
                    @error('tracer_frequency')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Bulan Mulai Tracer --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Bulan Mulai Tracer <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="tracer_month"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                    @error('tracer_month')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Durasi Pengisian (Hari) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Durasi Pengisian (Hari) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" wire:model="tracer_duration_days" min="30" max="365"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="90">
                    @error('tracer_duration_days')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Catatan:</strong> Pengaturan ini menentukan kapan alumni dapat mengisi atau memperbarui
                    tracer study mereka.
                </p>
            </div>
        </div>

        {{-- ========== BUTTONS ========== --}}
        <div class="flex gap-3">
            <button type="submit"
                class="px-8 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                💾 Simpan Pengaturan
            </button>
            <button type="button" wire:click="reset"
                class="px-8 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
                ↻ Reset
            </button>
        </div>
    </form>
</div>
