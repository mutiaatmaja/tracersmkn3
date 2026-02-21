<?php

use App\Imports\AlumnisImport;
use App\Models\Alumni;
use App\Models\Competency;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithFileUploads;

    public string $search = '';

    public int $limit = 20;

    public array $competencies = [];

    public bool $showModal = false;

    public ?Alumni $editingAlumni = null;

    public $importFile;

    public string $namaLengkap = '';

    public string $nisn = '';

    public string $nik = '';

    public ?int $competencyId = null;

    public ?int $tahunLulus = null;

    public string $jenisKelamin = '';

    public string $tempatLahir = '';

    public string $tanggalLahir = '';

    public string $nomorTelepon = '';

    public string $alamat = '';

    public function mount(): void
    {
        $this->loadCompetencies();
    }

    public function render()
    {
        return view('livewire.pages.alumnis');
    }

    public function loadCompetencies(): void
    {
        $this->competencies = Competency::query()->aktif()->orderBy('nama')->get()->toArray();
    }

    public function updatedSearch(): void
    {
        $this->limit = 20;
    }

    #[Computed]
    public function alumnis()
    {
        $query = Alumni::query()->with('competency')->orderByDesc('id');

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhereHas('competency', function ($competencyQuery) {
                        $competencyQuery->where('nama', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->limit($this->limit)->get();
    }

    #[Computed]
    public function totalAlumnis(): int
    {
        $query = Alumni::query();

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%');
            });
        }

        return $query->count();
    }

    public function loadMore(): void
    {
        if ($this->limit < $this->totalAlumnis) {
            $this->limit += 20;
        }
    }

    public function createAlumni(): void
    {
        $this->editingAlumni = null;
        $this->namaLengkap = '';
        $this->nisn = '';
        $this->nik = '';
        $this->competencyId = null;
        $this->tahunLulus = null;
        $this->jenisKelamin = '';
        $this->tempatLahir = '';
        $this->tanggalLahir = '';
        $this->nomorTelepon = '';
        $this->alamat = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editAlumni(Alumni $alumni): void
    {
        $this->editingAlumni = $alumni;
        $this->namaLengkap = $alumni->nama_lengkap ?? '';
        $this->nisn = $alumni->nisn ?? '';
        $this->nik = $alumni->nik ?? '';
        $this->competencyId = $alumni->competency_id;
        $this->tahunLulus = $alumni->tahun_lulus;
        $this->jenisKelamin = $alumni->jenis_kelamin;
        $this->tempatLahir = $alumni->tempat_lahir ?? '';
        $this->tanggalLahir = $alumni->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->nomorTelepon = $alumni->nomor_telepon ?? '';
        $this->alamat = $alumni->alamat ?? '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function saveAlumni(): void
    {
        $validated = $this->validate([
            'namaLengkap' => 'required|string|max:255',
            'nisn' => ['nullable', 'string', 'max:30', Rule::unique('alumnis', 'nisn')->ignore($this->editingAlumni?->id)],
            'nik' => ['nullable', 'string', 'max:30', Rule::unique('alumnis', 'nik')->ignore($this->editingAlumni?->id)],
            'competencyId' => 'required|exists:competencies,id',
            'tahunLulus' => 'required|integer|min:1900|max:' . (now()->year + 1),
            'jenisKelamin' => 'required|in:laki-laki,perempuan',
            'tempatLahir' => 'nullable|string|max:255',
            'tanggalLahir' => 'nullable|date',
            'nomorTelepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:1000',
        ]);

        if (blank($validated['nisn']) && blank($validated['nik'])) {
            $this->addError('nisn', 'NISN atau NIK wajib diisi salah satu.');
            $this->addError('nik', 'NISN atau NIK wajib diisi salah satu.');

            return;
        }

        $payload = [
            'nama_lengkap' => $validated['namaLengkap'],
            'nisn' => filled($validated['nisn']) ? $validated['nisn'] : null,
            'nik' => filled($validated['nik']) ? $validated['nik'] : null,
            'competency_id' => $validated['competencyId'],
            'tahun_lulus' => $validated['tahunLulus'],
            'jenis_kelamin' => $validated['jenisKelamin'],
            'tempat_lahir' => filled($validated['tempatLahir']) ? $validated['tempatLahir'] : null,
            'tanggal_lahir' => filled($validated['tanggalLahir']) ? $validated['tanggalLahir'] : null,
            'nomor_telepon' => filled($validated['nomorTelepon']) ? $validated['nomorTelepon'] : null,
            'alamat' => filled($validated['alamat']) ? $validated['alamat'] : null,
        ];

        if ($this->editingAlumni) {
            $this->editingAlumni->update($payload);
            $this->dispatch('toast', message: 'Data alumni berhasil diperbarui!', type: 'success');
        } else {
            Alumni::query()->create($payload);
            $this->dispatch('toast', message: 'Data alumni berhasil ditambahkan!', type: 'success');
        }

        $this->showModal = false;
        unset($this->alumnis, $this->totalAlumnis);
    }

    public function deleteAlumni(Alumni $alumni): void
    {
        $alumni->delete();

        $this->dispatch('toast', message: 'Data alumni berhasil dihapus!', type: 'success');
        unset($this->alumnis, $this->totalAlumnis);
    }

    public function importAlumnis(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new AlumnisImport();
        Excel::import($import, $this->importFile);

        $this->reset('importFile');
        unset($this->alumnis, $this->totalAlumnis);

        $this->dispatch('toast', message: "Import selesai. Baru: {$import->createdCount}, Diperbarui: {$import->updatedCount}, Dilewati: {$import->skippedCount}", type: 'success');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Data Alumni</h1>
            <p class="mt-1 text-gray-600">Kelola biodata alumni, status klaim akun, dan progres tracer study</p>
        </div>
        <button wire:click="createAlumni" wire:loading.attr="disabled" wire:target="createAlumni"
            class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="createAlumni">➕ Tambah Alumni</span>
            <span wire:loading wire:target="createAlumni">Membuka...</span>
        </button>
    </div>

    <div class="space-y-3 rounded-lg border border-gray-200 bg-white p-4">
        <input type="text" wire:model.live="search" placeholder="Cari nama/NISN/NIK/kompetensi..."
            class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('alumnis.dummy.download') }}"
                class="order-3 inline-flex min-w-36 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100 sm:order-1">
                Download Dummy
            </a>
            <input type="file" wire:model="importFile" accept=".xlsx,.xls,.csv"
                class="order-1 w-full min-w-0 rounded-lg border border-gray-300 px-4 py-2 text-sm transition focus:border-transparent focus:ring-2 focus:ring-blue-500 sm:order-2 sm:min-w-55 sm:flex-1" />
            <button wire:click="importAlumnis" wire:loading.attr="disabled" wire:target="importAlumnis,importFile"
                class="order-2 inline-flex min-w-36 items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100 disabled:cursor-not-allowed disabled:opacity-60 sm:order-3">
                <span wire:loading.remove wire:target="importAlumnis">Import</span>
                <span wire:loading wire:target="importAlumnis">...</span>
            </button>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Biodata</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Kompetensi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Lulus</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Klaim</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->alumnis as $index => $alumni)
                    <tr wire:key="alumni-{{ $alumni->id }}" class="transition hover:bg-gray-50">
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">
                            <p class="font-semibold text-gray-900">{{ $alumni->nama_lengkap ?: '-' }}</p>
                            <p>NISN: {{ $alumni->nisn ?: '-' }}</p>
                            <p>NIK: {{ $alumni->nik ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $alumni->competency->nama }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $alumni->tahun_lulus }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">
                            @if ($alumni->is_claimed)
                                <span
                                    class="rounded border border-green-200 bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span
                                    class="rounded border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">Belum</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="inline-flex items-center gap-1.5">
                                <button wire:click="editAlumni({{ $alumni->id }})" wire:loading.attr="disabled"
                                    wire:target="editAlumni({{ $alumni->id }})"
                                    class="rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 disabled:cursor-not-allowed disabled:opacity-60">
                                    <span wire:loading.remove wire:target="editAlumni({{ $alumni->id }})">Edit</span>
                                    <span wire:loading wire:target="editAlumni({{ $alumni->id }})">Membuka...</span>
                                </button>
                                <button wire:click="deleteAlumni({{ $alumni->id }})"
                                    onclick="return confirm('Yakin hapus data alumni ini?')"
                                    class="rounded border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100">Hapus</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data alumni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->alumnis->count() > 0)
            <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $this->alumnis->count() }}</span> dari
                        <span class="font-semibold">{{ $this->totalAlumnis }}</span> total alumni
                    </p>

                    @if ($this->alumnis->count() < $this->totalAlumnis)
                        <div wire:loading.remove wire:target="loadMore">
                            <span class="text-sm text-blue-600">Scroll ke bawah untuk memuat lebih banyak...</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if ($this->alumnis->count() > 0 && $this->alumnis->count() < $this->totalAlumnis)
        <div wire:intersect="loadMore" class="h-10"></div>
    @endif

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black bg-opacity-30 p-4 backdrop-blur-sm md:items-center"
            wire:click="closeModal">
            <div class="my-4 w-full max-w-3xl rounded-lg bg-white p-6 shadow-2xl md:my-8 md:max-h-[90vh] md:overflow-y-auto"
                wire:click.stop>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">{{ $editingAlumni ? 'Edit Alumni' : 'Tambah Alumni' }}
                    </h2>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveAlumni" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" wire:model.defer="namaLengkap"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('namaLengkap')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kompetensi</label>
                            <select wire:model.defer="competencyId"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Kompetensi</option>
                                @foreach ($competencies as $competency)
                                    <option value="{{ $competency['id'] }}">{{ $competency['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('competencyId')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">NISN</label>
                            <input type="text" wire:model.defer="nisn"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('nisn')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">NIK</label>
                            <input type="text" wire:model.defer="nik"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('nik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tahun Lulus</label>
                            <input type="number" wire:model.defer="tahunLulus"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('tahunLulus')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select wire:model.defer="jenisKelamin"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih</option>
                                <option value="laki-laki">Laki-laki</option>
                                <option value="perempuan">Perempuan</option>
                            </select>
                            @error('jenisKelamin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tempat Lahir</label>
                            <input type="text" wire:model.defer="tempatLahir"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('tempatLahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" wire:model.defer="tanggalLahir"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('tanggalLahir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" wire:model.defer="nomorTelepon"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500" />
                            @error('nomorTelepon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                        <textarea wire:model.defer="alamat" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2 transition focus:border-transparent focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg bg-gray-100 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-200">Batal</button>
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-sm transition hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
