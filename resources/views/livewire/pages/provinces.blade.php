<?php

use App\Models\Province;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire Component untuk CRUD Provinsi
 */
new class extends Component {
    public $provinces = [];

    public $search = '';

    public bool $showModal = false;

    public ?Province $editingProvince = null;

    #[Validate('required|string|max:10|unique:provinces,kode')]
    public string $kode = '';

    #[Validate('required|string|max:255')]
    public string $nama = '';

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->loadProvinces();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.pages.provinces');
    }

    /**
     * Load data provinces
     */
    public function loadProvinces(): void
    {
        $query = Province::query()->with('cities');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')->orWhere('kode', 'like', '%' . $this->search . '%');
            });
        }

        $this->provinces = $query->orderBy('kode')->get();
    }

    /**
     * Update search
     */
    public function updatedSearch(): void
    {
        $this->loadProvinces();
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
    public function edit(Province $province): void
    {
        $this->editingProvince = $province;
        $this->kode = $province->kode;
        $this->nama = $province->nama;
        $this->showModal = true;
    }

    /**
     * Simpan data
     */
    public function save(): void
    {
        if ($this->editingProvince) {
            $this->validate([
                'kode' => 'required|string|max:10|unique:provinces,kode,' . $this->editingProvince->id,
                'nama' => 'required|string|max:255',
            ]);

            $this->editingProvince->update([
                'kode' => $this->kode,
                'nama' => $this->nama,
            ]);

            $this->dispatch('toast', message: 'Provinsi berhasil diperbarui!', type: 'success');
        } else {
            $this->validate();

            Province::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
            ]);

            $this->dispatch('toast', message: 'Provinsi berhasil ditambahkan!', type: 'success');
        }

        $this->closeModal();
        $this->loadProvinces();
    }

    /**
     * Hapus data
     */
    public function delete(Province $province): void
    {
        if ($province->cities()->count() > 0) {
            $this->dispatch('toast', message: 'Tidak bisa hapus provinsi yang sudah memiliki kota/kabupaten!', type: 'error');

            return;
        }

        $province->delete();
        $this->dispatch('toast', message: 'Provinsi berhasil dihapus!', type: 'success');
        $this->loadProvinces();
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
        $this->editingProvince = null;
        $this->kode = '';
        $this->nama = '';
        $this->resetValidation();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Provinsi</h1>
            <p class="text-gray-600 mt-1">Kelola data provinsi di Indonesia</p>
        </div>
        <button wire:click="create" wire:loading.attr="disabled" wire:target="create"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="create">➕ Tambah Provinsi</span>
            <span wire:loading wire:target="create">Membuka...</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <input type="text" wire:model.live="search" placeholder="Cari provinsi..."
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Provinsi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Jumlah Kota/Kab</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($provinces as $province)
                    <tr wire:key="province-{{ $province->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $province->kode }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $province->nama }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $province->cities->count() }} kota/kabupaten</td>
                        <td class="px-6 py-4 text-sm text-right space-x-2">
                            <button wire:click="edit({{ $province->id }})" wire:loading.attr="disabled"
                                wire:target="edit({{ $province->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="edit({{ $province->id }})">✏️ Edit</span>
                                <span wire:loading wire:target="edit({{ $province->id }})">Membuka...</span>
                            </button>
                            <button wire:click="delete({{ $province->id }})"
                                class="text-red-600 hover:text-red-800 font-medium"
                                onclick="return confirm('Yakin hapus provinsi ini?')">
                                🗑️ Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada data provinsi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingProvince ? 'Edit Provinsi' : 'Tambah Provinsi' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Provinsi</label>
                        <input type="text" wire:model.defer="kode" {{ $editingProvince ? 'disabled readonly' : '' }}
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 {{ $editingProvince ? 'bg-gray-100' : '' }}" />
                        @error('kode')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Provinsi</label>
                        <input type="text" wire:model.defer="nama"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('nama')
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
                            {{ $editingProvince ? '💾 Simpan' : '➕ Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
