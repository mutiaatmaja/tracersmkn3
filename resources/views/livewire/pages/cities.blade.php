<?php

use App\Models\City;
use App\Models\Province;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire Component untuk CRUD Kabupaten/Kota dengan Lazy Loading
 */
new class extends Component {
    public $provinces = [];

    public $search = '';

    public $filterProvince = '';

    public int $limit = 20;

    public bool $showModal = false;

    public ?City $editingCity = null;

    #[Validate('required|string|max:10|unique:cities,kode')]
    public string $kode = '';

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|exists:provinces,id')]
    public ?int $province_id = null;

    #[Validate('required|in:kabupaten,kota')]
    public string $tipe = '';

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->provinces = Province::orderBy('nama')->get();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.pages.cities');
    }

    /**
     * Get cities dengan lazy loading (computed property)
     */
    #[Computed]
    public function cities()
    {
        $query = City::query()->with('province');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')->orWhere('kode', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterProvince !== '') {
            $query->where('province_id', $this->filterProvince);
        }

        return $query->orderBy('province_id')->orderBy('nama')->limit($this->limit)->get();
    }

    /**
     * Load more cities saat scroll ke bawah
     */
    public function loadMore(): void
    {
        $this->limit += 20;
    }

    /**
     * Update search - reset limit
     */
    public function updatedSearch(): void
    {
        $this->limit = 20;
    }

    /**
     * Update filter province - reset limit
     */
    public function updatedFilterProvince(): void
    {
        $this->limit = 20;
    }

    /**
     * Buka modal untuk tambah
     */
    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Buka modal untuk edit
     */
    public function edit(City $city): void
    {
        $this->editingCity = $city;
        $this->kode = $city->kode;
        $this->nama = $city->nama;
        $this->province_id = $city->province_id;
        $this->tipe = $city->tipe;
        $this->showModal = true;
    }

    /**
     * Simpan data
     */
    public function save(): void
    {
        if ($this->editingCity) {
            $this->validate([
                'kode' => 'required|string|max:10|unique:cities,kode,' . $this->editingCity->id,
                'nama' => 'required|string|max:255',
                'province_id' => 'required|exists:provinces,id',
                'tipe' => 'required|in:kabupaten,kota',
            ]);

            $this->editingCity->update([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'province_id' => $this->province_id,
                'tipe' => $this->tipe,
            ]);

            $this->dispatch('toast', message: 'Kota/Kabupaten berhasil diperbarui!', type: 'success');
        } else {
            $this->validate();

            City::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'province_id' => $this->province_id,
                'tipe' => $this->tipe,
            ]);

            $this->dispatch('toast', message: 'Kota/Kabupaten berhasil ditambahkan!', type: 'success');
        }

        $this->closeModal();
        unset($this->cities); // Clear computed cache
    }

    /**
     * Hapus data
     */
    public function delete(City $city): void
    {
        $city->delete();
        $this->dispatch('toast', message: 'Kota/Kabupaten berhasil dihapus!', type: 'success');
        unset($this->cities); // Clear computed cache
    }

    /**
     * Close modal
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset form
     */
    private function resetForm(): void
    {
        $this->editingCity = null;
        $this->kode = '';
        $this->nama = '';
        $this->province_id = null;
        $this->tipe = '';
        $this->resetValidation();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kabupaten / Kota</h1>
            <p class="text-gray-600 mt-1">Kelola data kabupaten dan kota di Indonesia</p>
        </div>
        <button wire:click="create" wire:loading.attr="disabled" wire:target="create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="create">➕ Tambah Kota/Kabupaten</span>
            <span wire:loading wire:target="create">Membuka...</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.live="search" placeholder="Cari kota/kabupaten..."
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            <select wire:model.live="filterProvince"
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Provinsi</option>
                @foreach ($provinces as $province)
                    <option value="{{ $province->id }}">{{ $province->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Tipe</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Provinsi</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->cities as $city)
                    <tr wire:key="city-{{ $city->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $city->kode }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $city->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $city->tipe === 'kota' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($city->tipe) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $city->province->nama }}</td>
                        <td class="px-6 py-4 text-sm text-right space-x-2">
                            <button wire:click="edit({{ $city->id }})" wire:loading.attr="disabled"
                                wire:target="edit({{ $city->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="edit({{ $city->id }})">✏️ Edit</span>
                                <span wire:loading wire:target="edit({{ $city->id }})">Membuka...</span>
                            </button>
                            <button wire:click="delete({{ $city->id }})"
                                class="text-red-600 hover:text-red-800 font-medium"
                                onclick="return confirm('Yakin hapus kota/kabupaten ini?')">
                                🗑️ Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data kota/kabupaten
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Info & Lazy Loading Trigger --}}
        @if ($this->cities->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $this->cities->count() }}</span> dari
                        <span class="font-semibold">{{ City::count() }}</span> total kota/kabupaten
                    </p>

                    @if ($this->cities->count() < City::count())
                        <div wire:loading.remove wire:target="loadMore">
                            <span class="text-sm text-blue-600">Scroll ke bawah untuk memuat lebih banyak...</span>
                        </div>
                    @endif
                </div>

                {{-- Loading Indicator --}}
                <div wire:loading wire:target="loadMore" class="mt-4 text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium">Memuat data...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Lazy Load Trigger (invisible div yang akan trigger loadMore saat terlihat) --}}
    @if ($this->cities->count() > 0 && $this->cities->count() < City::count())
        <div wire:intersect="loadMore" class="h-10"></div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingCity ? 'Edit Kota/Kabupaten' : 'Tambah Kota/Kabupaten' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                        <input type="text" wire:model.defer="kode" {{ $editingCity ? 'disabled readonly' : '' }}
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 {{ $editingCity ? 'bg-gray-100' : '' }}" />
                        @error('kode')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kota/Kabupaten</label>
                        <input type="text" wire:model.defer="nama"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('nama')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                        <select wire:model.defer="province_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->nama }}</option>
                            @endforeach
                        </select>
                        @error('province_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select wire:model.defer="tipe"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Tipe</option>
                            <option value="kabupaten">Kabupaten</option>
                            <option value="kota">Kota</option>
                        </select>
                        @error('tipe')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-semibold transition shadow-sm">
                            {{ $editingCity ? '💾 Simpan' : '➕ Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
