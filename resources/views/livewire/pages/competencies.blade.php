<?php

use App\Models\Competency;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    // Data untuk list competencies
    public $competencies = [];

    public $search = '';

    public $showForm = false;

    public ?Competency $editingCompetency = null;

    // Data form
    #[Validate('required|unique:competencies,kode')]
    public string $kode = '';

    #[Validate('required|max:255')]
    public string $nama = '';

    #[Validate('nullable|string')]
    public string $deskripsi = '';

    public bool $aktif = true;

    /**
     * Mount component - dijalankan saat component di-load
     */
    public function mount()
    {
        $this->loadCompetencies();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.pages.competencies');
    }

    /**
     * Filter dan load competencies berdasarkan search
     */
    public function loadCompetencies()
    {
        $this->competencies = Competency::where('nama', 'like', "%{$this->search}%")
            ->orWhere('kode', 'like', "%{$this->search}%")
            ->orderBy('kode')
            ->get()
            ->toArray();
    }

    /**
     * Handle pencarian realtime
     */
    #[\Livewire\Attributes\On('update:search')]
    public function updatedSearch()
    {
        $this->loadCompetencies();
    }

    /**
     * Buka form untuk menambah kompetensi baru
     */
    public function openCreateForm()
    {
        $this->reset('kode', 'nama', 'deskripsi', 'aktif');
        $this->editingCompetency = null;
        $this->showForm = true;
    }

    /**
     * Buka form untuk edit kompetensi
     */
    public function edit(Competency $competency)
    {
        $this->editingCompetency = $competency;
        $this->kode = $competency->kode;
        $this->nama = $competency->nama;
        $this->deskripsi = $competency->deskripsi ?? '';
        $this->aktif = $competency->aktif;
        $this->showForm = true;
    }

    /**
     * Simpan kompetensi (create/update)
     */
    public function save()
    {
        // Jika edit mode, update validasi kode
        if ($this->editingCompetency) {
            $this->validate([
                'kode' => 'required|unique:competencies,kode,' . $this->editingCompetency->id,
                'nama' => 'required|max:255',
                'deskripsi' => 'nullable|string',
            ]);

            $this->editingCompetency->update([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'deskripsi' => $this->deskripsi,
                'aktif' => $this->aktif,
            ]);

            $this->dispatch('toast', message: 'Kompetensi berhasil diperbarui!', type: 'success');
        } else {
            // Create mode
            $this->validate();

            Competency::create([
                'kode' => $this->kode,
                'nama' => $this->nama,
                'deskripsi' => $this->deskripsi,
                'aktif' => $this->aktif,
            ]);

            $this->dispatch('toast', message: 'Kompetensi berhasil ditambahkan!', type: 'success');
        }

        $this->closeForm();
        $this->loadCompetencies();
    }

    /**
     * Hapus kompetensi
     */
    public function delete(Competency $competency)
    {
        // Cek apakah ada alumni yang menggunakan kompetensi ini
        if ($competency->alumnis()->count() > 0) {
            $this->dispatch('toast', message: 'Tidak bisa hapus kompetensi yang sudah digunakan alumni!', type: 'error');

            return;
        }

        $competency->delete();
        $this->dispatch('toast', message: 'Kompetensi berhasil dihapus!', type: 'success');
        $this->closeForm();
        $this->loadCompetencies();
    }

    /**
     * Tutup form dan reset data
     */
    public function closeForm()
    {
        $this->showForm = false;
        $this->editingCompetency = null;
        $this->reset('kode', 'nama', 'deskripsi', 'aktif');
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleAktif(Competency $competency)
    {
        $competency->update(['aktif' => !$competency->aktif]);
        $this->loadCompetencies();
        $this->dispatch('toast', message: 'Status kompetensi berhasil diubah!', type: 'success');
    }
};
?>

<div class="space-y-6">
    {{-- ========== HEADER ========== --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Kompetensi</h1>
            <p class="text-gray-600 mt-1">Kelola kompetensi keahlian/jurusan yang ada di sekolah</p>
        </div>
        <button wire:click="openCreateForm"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
            + Tambah Kompetensi
        </button>
    </div>

    {{-- ========== SEARCH ========== --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <input type="text" wire:model.live="search" placeholder="Cari kompetensi (nama/kode)..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
        </div>
    </div>

    {{-- ========== FORM (Modal dengan Blur Background) ========== --}}
    @if ($showForm)
        <div
            class="fixed inset-0 bg-white bg-opacity-10 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
            <div
                class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8 transform transition-all duration-300 scale-100 animate-slideUp">
                {{-- Header Modal --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $editingCompetency ? '✏️ Edit Kompetensi' : '➕ Tambah Kompetensi Baru' }}
                    </h2>
                    <button wire:click="closeForm" type="button" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kode Kompetensi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="kode" placeholder="RPL, TKJ, dll"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            {{ $editingCompetency ? 'disabled readonly' : '' }}>
                        @if ($editingCompetency)
                            <p class="text-xs text-gray-500 mt-1">
                                💡 Kode tidak bisa diubah untuk menjaga integritas data alumni
                            </p>
                        @endif
                        @error('kode')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Kompetensi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="nama" placeholder="Rekayasa Perangkat Lunak"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('nama')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi
                        </label>
                        <textarea wire:model="deskripsi" placeholder="Deskripsi kompetensi..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            rows="3"></textarea>
                    </div>

                    {{-- Aktif --}}
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="aktif" id="aktif"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <label for="aktif" class="text-sm font-medium text-gray-700">
                            Status Aktif
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 mt-8">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 active:scale-95 transition-all duration-200 shadow-md hover:shadow-lg">
                            {{ $editingCompetency ? '✓ Perbarui' : '➕ Tambahkan' }}
                        </button>
                        <button type="button" wire:click="closeForm"
                            class="flex-1 bg-gray-200 text-gray-700 py-2.5 rounded-lg font-semibold hover:bg-gray-300 active:scale-95 transition-all duration-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ========== TABLE ========== --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if (count($competencies) > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kode</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nama Kompetensi</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($competencies as $competency)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-blue-600">{{ $competency['kode'] }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $competency['nama'] }}</td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ Str::limit($competency['deskripsi'] ?? '-', 50) }}
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleAktif({{ $competency['id'] }})"
                                    class="inline-block px-3 py-1 rounded-full text-sm font-semibold transition
                                    {{ $competency['aktif'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $competency['aktif'] ? '✓ Aktif' : '✗ Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $competency['id'] }})"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold hover:bg-blue-200 transition">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $competency['id'] }})"
                                        onclick="confirm('Yakin hapus kompetensi ini?') || event.stopImmediatePropagation()"
                                        class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-sm font-semibold hover:bg-red-200 transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12 text-gray-500">
                <p class="text-lg">Belum ada kompetensi. Silakan tambahkan kompetensi baru.</p>
            </div>
        @endif
    </div>
</div>
