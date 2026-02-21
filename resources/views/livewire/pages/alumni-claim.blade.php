<?php

use App\Models\Alumni;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public string $identifier = '';

    public ?Alumni $foundAlumni = null;

    public bool $showClaimForm = false;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function render()
    {
        return view('livewire.pages.alumni-claim');
    }

    public function searchAlumni(): void
    {
        $validated = $this->validate([
            'identifier' => 'required|string|max:30',
        ]);

        $this->foundAlumni = Alumni::query()->with('competency')->where('nisn', $validated['identifier'])->orWhere('nik', $validated['identifier'])->first();

        $this->showClaimForm = false;
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';

        if (!$this->foundAlumni) {
            $this->dispatch('toast', message: 'Data alumni tidak ditemukan.', type: 'error');

            return;
        }

        if ($this->foundAlumni->is_claimed || $this->foundAlumni->user_id) {
            $this->dispatch('toast', message: 'Data alumni ini sudah pernah diklaim.', type: 'error');
            $this->foundAlumni = null;
        }
    }

    public function confirmDataOwnership(): void
    {
        if (!$this->foundAlumni) {
            $this->dispatch('toast', message: 'Data alumni belum dipilih.', type: 'error');

            return;
        }

        $this->showClaimForm = true;
    }

    public function claimAccount(): void
    {
        $validated = $this->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!$this->foundAlumni) {
            $this->dispatch('toast', message: 'Data alumni belum dipilih.', type: 'error');

            return;
        }

        $alumni = Alumni::query()->find($this->foundAlumni->id);

        if (!$alumni || $alumni->is_claimed || $alumni->user_id) {
            $this->dispatch('toast', message: 'Data alumni sudah tidak tersedia untuk klaim.', type: 'error');

            return;
        }

        $user = User::query()->create([
            'name' => $alumni->nama_lengkap ?: 'Alumni',
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_default_password' => false,
            'default_password_plain' => null,
        ]);

        $alumniRole = Role::query()->where('name', 'alumni')->first();

        if ($alumniRole) {
            $user->addRole($alumniRole);
        }

        $alumni->update([
            'user_id' => $user->id,
            'email_pribadi' => $validated['email'],
            'is_claimed' => true,
            'claimed_at' => now(),
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        $this->dispatch('toast', message: 'Akun berhasil diaktifkan. Selamat datang!', type: 'success');
        $this->redirectRoute('home', navigate: true);
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold text-gray-900">Klaim Akun Alumni</h1>
        <p class="mt-1 text-gray-600">Masukkan NISN atau NIK untuk menemukan data Anda.</p>

        <form wire:submit="searchAlumni" class="mt-4 flex flex-col gap-3 md:flex-row">
            <input type="text" wire:model.defer="identifier" placeholder="Contoh: NISN/NIK"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700">Cari
                Data</button>
        </form>
        @error('identifier')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if ($foundAlumni)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Data Ditemukan</h2>
            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-700 md:grid-cols-2">
                <p><span class="font-medium text-gray-900">Nama:</span> {{ $foundAlumni->nama_lengkap ?: '-' }}</p>
                <p><span class="font-medium text-gray-900">NISN:</span> {{ $foundAlumni->nisn ?: '-' }}</p>
                <p><span class="font-medium text-gray-900">NIK:</span> {{ $foundAlumni->nik ?: '-' }}</p>
                <p><span class="font-medium text-gray-900">Kompetensi:</span> {{ $foundAlumni->competency?->nama }}</p>
                <p><span class="font-medium text-gray-900">Tahun Lulus:</span> {{ $foundAlumni->tahun_lulus }}</p>
            </div>

            @if (!$showClaimForm)
                <button wire:click="confirmDataOwnership"
                    class="mt-4 rounded-lg bg-green-600 px-4 py-2 font-semibold text-white transition hover:bg-green-700">
                    Benar, ini data saya
                </button>
            @endif
        </div>
    @endif

    @if ($showClaimForm && $foundAlumni)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Aktivasi Akun</h2>
            <p class="mt-1 text-sm text-gray-600">Isi email dan password untuk login ke dashboard alumni.</p>

            <form wire:submit="claimAccount" class="mt-4 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model.defer="email"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model.defer="password"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" wire:model.defer="password_confirmation"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <button type="submit"
                    class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700">Aktifkan
                    Akun & Masuk</button>
            </form>
        </div>
    @endif
</div>
