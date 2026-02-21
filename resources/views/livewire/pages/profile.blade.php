<?php

use App\Models\Alumni;
use App\Models\Competency;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Livewire Component untuk halaman Profil
 */
new class extends Component {
    public int $userId;

    public ?int $alumniId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isAlumni = false;

    public ?string $nisn = null;

    public ?string $nik = null;

    public ?int $competency_id = null;

    public ?int $tahun_lulus = null;

    public ?string $jenis_kelamin = null;

    public ?string $foto_profil = null;

    public ?string $link_media_sosial = null;

    public bool $saved = false;

    /**
     * Mount component
     */
    public function mount(): void
    {
        $user = auth()->user();

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->isAlumni = $user->hasRole('alumni');

        if ($this->isAlumni) {
            $alumni = $user->alumni;

            if ($alumni) {
                $this->alumniId = $alumni->id;
                $this->nisn = $alumni->nisn;
                $this->nik = $alumni->nik;
                $this->competency_id = $alumni->competency_id;
                $this->tahun_lulus = $alumni->tahun_lulus;
                $this->jenis_kelamin = $alumni->jenis_kelamin;
                $this->foto_profil = $alumni->foto_profil;
                $this->link_media_sosial = $alumni->link_media_sosial;
            }
        }
    }

    /**
     * Render component
     */
    public function render(): View
    {
        $competencies = Competency::query()->aktif()->orderBy('nama')->get();

        return view('livewire.pages.profile', [
            'competencies' => $competencies,
        ]);
    }

    /**
     * Rules validasi
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($this->isAlumni) {
            $rules = array_merge($rules, [
                'nisn' => ['required', 'string', 'max:20', Rule::unique('alumnis', 'nisn')->ignore($this->alumniId)],
                'nik' => ['nullable', 'string', 'max:20', Rule::unique('alumnis', 'nik')->ignore($this->alumniId)],
                'competency_id' => ['required', 'exists:competencies,id'],
                'tahun_lulus' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'foto_profil' => ['nullable', 'string', 'max:255'],
                'link_media_sosial' => ['nullable', 'url', 'max:255'],
            ]);
        }

        return $rules;
    }

    /**
     * Simpan perubahan profil
     */
    public function save(): void
    {
        $this->validate();

        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->password !== '') {
            $user->update([
                'password' => $this->password,
                'is_default_password' => false,
                'default_password_plain' => null,
            ]);

            $this->password = '';
            $this->password_confirmation = '';
        }

        if ($this->isAlumni) {
            $alumni = $user->alumni ?? new Alumni(['user_id' => $user->id]);

            $alumni->fill([
                'nisn' => $this->nisn,
                'nik' => $this->nik,
                'competency_id' => $this->competency_id,
                'tahun_lulus' => $this->tahun_lulus,
                'jenis_kelamin' => $this->jenis_kelamin,
                'foto_profil' => $this->foto_profil,
                'link_media_sosial' => $this->link_media_sosial,
            ]);

            $alumni->save();
        }

        $this->saved = true;
        $this->dispatch('toast', message: 'Profil berhasil disimpan!', type: 'success');
    }
};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Profil</h1>
        <p class="text-gray-600 mt-1">Kelola informasi akun dan data pribadi</p>
    </div>

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
                <h3 class="font-semibold text-green-800">Profil berhasil disimpan!</h3>
                <p class="text-sm text-green-700">Perubahan sudah tersimpan.</p>
            </div>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Akun</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" wire:model.defer="name"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" wire:model.defer="password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Kosongkan jika tidak diganti" />
                    @error('password')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" wire:model.defer="password_confirmation"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Ulangi password baru" />
                </div>
            </div>
        </div>

        @if ($isAlumni)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Data Alumni</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                        <input type="text" wire:model.defer="nisn"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('nisn')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" wire:model.defer="nik"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('nik')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kompetensi</label>
                        <select wire:model.defer="competency_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Kompetensi</option>
                            @foreach ($competencies as $competency)
                                <option value="{{ $competency->id }}">{{ $competency->nama }}</option>
                            @endforeach
                        </select>
                        @error('competency_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
                        <input type="number" wire:model.defer="tahun_lulus"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('tahun_lulus')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select wire:model.defer="jenis_kelamin"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="laki-laki">Laki-laki</option>
                            <option value="perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil (URL/Path)</label>
                        <input type="text" wire:model.defer="foto_profil"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('foto_profil')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Media Sosial</label>
                        <input type="url" wire:model.defer="link_media_sosial"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="https://..." />
                        @error('link_media_sosial')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-end gap-3">
            <button type="submit"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
