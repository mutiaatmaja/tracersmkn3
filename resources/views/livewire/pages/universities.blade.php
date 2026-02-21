<?php

use App\Models\StudyProgram;
use App\Models\University;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Livewire Component untuk CRUD Perguruan Tinggi dan Program Studi
 */
new class extends Component {
    public string $search = '';

    public string $filterUniversity = '';

    public int $limit = 20;

    public bool $showUniversityModal = false;

    public bool $showStudyProgramModal = false;

    public ?University $editingUniversity = null;

    public ?StudyProgram $editingStudyProgram = null;

    #[Validate('required|string|max:20|unique:universities,kode')]
    public string $universityKode = '';

    #[Validate('required|string|max:255')]
    public string $universityNama = '';

    #[Validate('required|string|max:20|unique:study_programs,kode')]
    public string $studyProgramKode = '';

    #[Validate('required|string|max:255')]
    public string $studyProgramNama = '';

    #[Validate('required|exists:universities,id')]
    public ?int $studyProgramUniversityId = null;

    public function render()
    {
        return view('livewire.pages.universities');
    }

    #[Computed]
    public function universities()
    {
        return University::query()->withCount('studyPrograms')->orderBy('nama')->get();
    }

    #[Computed]
    public function studyPrograms()
    {
        $query = StudyProgram::query()->with('university');

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('study_programs.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('study_programs.kode', 'like', '%' . $this->search . '%')
                    ->orWhereHas('university', function ($universityQuery) {
                        $universityQuery->where('nama', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterUniversity !== '') {
            $query->where('university_id', $this->filterUniversity);
        }

        return $query
            ->orderBy(University::select('nama')->whereColumn('universities.id', 'study_programs.university_id')->limit(1))
            ->orderBy('study_programs.nama')
            ->limit($this->limit)
            ->get();
    }

    #[Computed]
    public function totalStudyPrograms(): int
    {
        $query = StudyProgram::query();

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('study_programs.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('study_programs.kode', 'like', '%' . $this->search . '%')
                    ->orWhereHas('university', function ($universityQuery) {
                        $universityQuery->where('nama', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->filterUniversity !== '') {
            $query->where('university_id', $this->filterUniversity);
        }

        return $query->count();
    }

    public function updatedSearch(): void
    {
        $this->limit = 20;
    }

    public function updatedFilterUniversity(): void
    {
        $this->limit = 20;
    }

    public function loadMore(): void
    {
        if ($this->limit < $this->totalStudyPrograms) {
            $this->limit += 20;
        }
    }

    public function createUniversity(): void
    {
        $this->editingUniversity = null;
        $this->universityKode = '';
        $this->universityNama = '';
        $this->resetValidation();
        $this->showUniversityModal = true;
    }

    public function editUniversity(University $university): void
    {
        $this->editingUniversity = $university;
        $this->universityKode = $university->kode;
        $this->universityNama = $university->nama;
        $this->resetValidation();
        $this->showUniversityModal = true;
    }

    public function saveUniversity(): void
    {
        if ($this->editingUniversity) {
            $this->validate([
                'universityKode' => 'required|string|max:20|unique:universities,kode,' . $this->editingUniversity->id,
                'universityNama' => 'required|string|max:255',
            ]);

            $this->editingUniversity->update([
                'kode' => $this->universityKode,
                'nama' => $this->universityNama,
            ]);

            $this->dispatch('toast', message: 'Perguruan tinggi berhasil diperbarui!', type: 'success');
        } else {
            $this->validate([
                'universityKode' => 'required|string|max:20|unique:universities,kode',
                'universityNama' => 'required|string|max:255',
            ]);

            University::create([
                'kode' => $this->universityKode,
                'nama' => $this->universityNama,
            ]);

            $this->dispatch('toast', message: 'Perguruan tinggi berhasil ditambahkan!', type: 'success');
        }

        $this->showUniversityModal = false;
        unset($this->universities, $this->studyPrograms, $this->totalStudyPrograms);
    }

    public function deleteUniversity(University $university): void
    {
        if ($this->filterUniversity !== '' && (int) $this->filterUniversity === $university->id) {
            $this->filterUniversity = '';
        }

        $university->delete();

        $this->dispatch('toast', message: 'Perguruan tinggi berhasil dihapus!', type: 'success');
        unset($this->universities, $this->studyPrograms, $this->totalStudyPrograms);
    }

    public function createStudyProgram(): void
    {
        $this->editingStudyProgram = null;
        $this->studyProgramKode = '';
        $this->studyProgramNama = '';
        $this->studyProgramUniversityId = null;
        $this->resetValidation();
        $this->showStudyProgramModal = true;
    }

    public function editStudyProgram(StudyProgram $studyProgram): void
    {
        $this->editingStudyProgram = $studyProgram;
        $this->studyProgramKode = $studyProgram->kode;
        $this->studyProgramNama = $studyProgram->nama;
        $this->studyProgramUniversityId = $studyProgram->university_id;
        $this->resetValidation();
        $this->showStudyProgramModal = true;
    }

    public function saveStudyProgram(): void
    {
        if ($this->editingStudyProgram) {
            $this->validate([
                'studyProgramKode' => 'required|string|max:20|unique:study_programs,kode,' . $this->editingStudyProgram->id,
                'studyProgramNama' => 'required|string|max:255',
                'studyProgramUniversityId' => 'required|exists:universities,id',
            ]);

            $this->editingStudyProgram->update([
                'kode' => $this->studyProgramKode,
                'nama' => $this->studyProgramNama,
                'university_id' => $this->studyProgramUniversityId,
            ]);

            $this->dispatch('toast', message: 'Program studi berhasil diperbarui!', type: 'success');
        } else {
            $this->validate([
                'studyProgramKode' => 'required|string|max:20|unique:study_programs,kode',
                'studyProgramNama' => 'required|string|max:255',
                'studyProgramUniversityId' => 'required|exists:universities,id',
            ]);

            StudyProgram::create([
                'kode' => $this->studyProgramKode,
                'nama' => $this->studyProgramNama,
                'university_id' => $this->studyProgramUniversityId,
            ]);

            $this->dispatch('toast', message: 'Program studi berhasil ditambahkan!', type: 'success');
        }

        $this->showStudyProgramModal = false;
        unset($this->universities, $this->studyPrograms, $this->totalStudyPrograms);
    }

    public function deleteStudyProgram(StudyProgram $studyProgram): void
    {
        $studyProgram->delete();

        $this->dispatch('toast', message: 'Program studi berhasil dihapus!', type: 'success');
        unset($this->universities, $this->studyPrograms, $this->totalStudyPrograms);
    }

    public function closeUniversityModal(): void
    {
        $this->showUniversityModal = false;
        $this->resetValidation();
    }

    public function closeStudyProgramModal(): void
    {
        $this->showStudyProgramModal = false;
        $this->resetValidation();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Perguruan Tinggi & Program Studi</h1>
            <p class="text-gray-600 mt-1">Kelola data perguruan tinggi dan seluruh program studi alumni</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="createUniversity" wire:loading.attr="disabled" wire:target="createUniversity"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-800 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="createUniversity">🏫 Tambah PT</span>
                <span wire:loading wire:target="createUniversity">Membuka...</span>
            </button>
            <button wire:click="createStudyProgram" wire:loading.attr="disabled" wire:target="createStudyProgram"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="createStudyProgram">➕ Tambah Prodi</span>
                <span wire:loading wire:target="createStudyProgram">Membuka...</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Data Perguruan Tinggi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse ($this->universities as $university)
                <div wire:key="university-{{ $university->id }}"
                    class="rounded-lg border border-gray-200 bg-gray-50 p-4 flex items-start justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ $university->kode }}</p>
                        <p class="font-semibold text-gray-900">{{ $university->nama }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $university->study_programs_count }} program studi</p>
                    </div>
                    <div class="text-sm space-x-2">
                        <button wire:click="editUniversity({{ $university->id }})" wire:loading.attr="disabled"
                            wire:target="editUniversity({{ $university->id }})"
                            class="text-blue-600 hover:text-blue-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="editUniversity({{ $university->id }})">✏️ Edit</span>
                            <span wire:loading wire:target="editUniversity({{ $university->id }})">Membuka...</span>
                        </button>
                        <button wire:click="deleteUniversity({{ $university->id }})"
                            class="text-red-600 hover:text-red-800 font-medium"
                            onclick="return confirm('Yakin hapus perguruan tinggi ini? Program studi terkait juga akan terhapus.')">🗑️
                            Hapus</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data perguruan tinggi.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" wire:model.live="search" placeholder="Cari program studi atau perguruan tinggi..."
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            <select wire:model.live="filterUniversity"
                class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Perguruan Tinggi</option>
                @foreach ($this->universities as $university)
                    <option value="{{ $university->id }}">{{ $university->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Perguruan Tinggi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Kode Prodi</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Program Studi</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->studyPrograms as $studyProgram)
                    <tr wire:key="study-program-{{ $studyProgram->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $studyProgram->university->nama }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $studyProgram->kode }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $studyProgram->nama }}</td>
                        <td class="px-6 py-4 text-sm text-right space-x-2">
                            <button wire:click="editStudyProgram({{ $studyProgram->id }})" wire:loading.attr="disabled"
                                wire:target="editStudyProgram({{ $studyProgram->id }})"
                                class="text-blue-600 hover:text-blue-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="editStudyProgram({{ $studyProgram->id }})">✏️
                                    Edit</span>
                                <span wire:loading
                                    wire:target="editStudyProgram({{ $studyProgram->id }})">Membuka...</span>
                            </button>
                            <button wire:click="deleteStudyProgram({{ $studyProgram->id }})"
                                class="text-red-600 hover:text-red-800 font-medium"
                                onclick="return confirm('Yakin hapus program studi ini?')">🗑️ Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Tidak ada data program studi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->studyPrograms->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $this->studyPrograms->count() }}</span> dari
                        <span class="font-semibold">{{ $this->totalStudyPrograms }}</span> total program studi
                    </p>

                    @if ($this->studyPrograms->count() < $this->totalStudyPrograms)
                        <div wire:loading.remove wire:target="loadMore">
                            <span class="text-sm text-blue-600">Scroll ke bawah untuk memuat lebih banyak...</span>
                        </div>
                    @endif
                </div>

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

    @if ($this->studyPrograms->count() > 0 && $this->studyPrograms->count() < $this->totalStudyPrograms)
        <div wire:intersect="loadMore" class="h-10"></div>
    @endif

    @if ($showUniversityModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeUniversityModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingUniversity ? 'Edit Perguruan Tinggi' : 'Tambah Perguruan Tinggi' }}
                    </h2>
                    <button wire:click="closeUniversityModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveUniversity" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                        <input type="text" wire:model.defer="universityKode"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('universityKode')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perguruan Tinggi</label>
                        <input type="text" wire:model.defer="universityNama"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('universityNama')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeUniversityModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-semibold transition shadow-sm">
                            {{ $editingUniversity ? '💾 Simpan' : '➕ Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showStudyProgramModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeStudyProgramModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingStudyProgram ? 'Edit Program Studi' : 'Tambah Program Studi' }}
                    </h2>
                    <button wire:click="closeStudyProgramModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveStudyProgram" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Perguruan Tinggi</label>
                        <select wire:model.defer="studyProgramUniversityId"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Perguruan Tinggi</option>
                            @foreach ($this->universities as $university)
                                <option value="{{ $university->id }}">{{ $university->nama }}</option>
                            @endforeach
                        </select>
                        @error('studyProgramUniversityId')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Program Studi</label>
                        <input type="text" wire:model.defer="studyProgramKode"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('studyProgramKode')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi</label>
                        <input type="text" wire:model.defer="studyProgramNama"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('studyProgramNama')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeStudyProgramModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-semibold transition shadow-sm">
                            {{ $editingStudyProgram ? '💾 Simpan' : '➕ Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
