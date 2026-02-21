<?php

use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    public string $search = '';

    public int $limit = 20;

    public $roles = [];

    public bool $showRoleModal = false;

    public bool $showUserModal = false;

    public bool $showChangeRoleModal = false;

    public ?Role $editingRole = null;

    public ?User $editingUser = null;

    public ?User $changingRoleUser = null;

    #[Validate('required|string|max:255|unique:roles,name')]
    public string $roleName = '';

    #[Validate('required|string|max:255')]
    public string $roleDisplayName = '';

    #[Validate('nullable|string|max:255')]
    public string $roleDescription = '';

    #[Validate('required|string|max:255')]
    public string $userName = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $userEmail = '';

    #[Validate('required|exists:roles,id')]
    public ?int $userRoleId = null;

    #[Validate('required|exists:roles,id')]
    public ?int $newRoleId = null;

    public function mount(): void
    {
        $this->loadRoles();
    }

    public function render()
    {
        return view('livewire.pages.users');
    }

    public function updatedSearch(): void
    {
        $this->limit = 20;
    }

    public function loadRoles(): void
    {
        $this->roles = Role::query()->orderBy('display_name')->orderBy('name')->get();
    }

    #[Computed]
    public function users()
    {
        $query = User::query()->with('roles');

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'like', '%' . $this->search . '%')->orWhere('display_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->orderBy('name')->limit($this->limit)->get();
    }

    #[Computed]
    public function totalUsers(): int
    {
        $query = User::query();

        if ($this->search !== '') {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'like', '%' . $this->search . '%')->orWhere('display_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        return $query->count();
    }

    public function loadMore(): void
    {
        if ($this->limit < $this->totalUsers) {
            $this->limit += 20;
        }
    }

    public function createRole(): void
    {
        $this->editingRole = null;
        $this->roleName = '';
        $this->roleDisplayName = '';
        $this->roleDescription = '';
        $this->resetValidation();
        $this->showRoleModal = true;
    }

    public function editRole(Role $role): void
    {
        $this->editingRole = $role;
        $this->roleName = $role->name;
        $this->roleDisplayName = $role->display_name ?? $role->name;
        $this->roleDescription = $role->description ?? '';
        $this->resetValidation();
        $this->showRoleModal = true;
    }

    public function saveRole(): void
    {
        if ($this->editingRole) {
            $this->validate([
                'roleName' => 'required|string|max:255|unique:roles,name,' . $this->editingRole->id,
                'roleDisplayName' => 'required|string|max:255',
                'roleDescription' => 'nullable|string|max:255',
            ]);

            $this->editingRole->update([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName,
                'description' => $this->roleDescription !== '' ? $this->roleDescription : null,
            ]);

            $this->dispatch('toast', message: 'Peran berhasil diperbarui!', type: 'success');
        } else {
            $this->validate([
                'roleName' => 'required|string|max:255|unique:roles,name',
                'roleDisplayName' => 'required|string|max:255',
                'roleDescription' => 'nullable|string|max:255',
            ]);

            Role::query()->create([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName,
                'description' => $this->roleDescription !== '' ? $this->roleDescription : null,
            ]);

            $this->dispatch('toast', message: 'Peran berhasil ditambahkan!', type: 'success');
        }

        $this->showRoleModal = false;
        $this->loadRoles();
        unset($this->users, $this->totalUsers);
    }

    public function deleteRole(Role $role): void
    {
        if ($role->users()->count() > 0) {
            $this->dispatch('toast', message: 'Peran tidak bisa dihapus karena masih digunakan pengguna.', type: 'error');

            return;
        }

        $role->delete();
        $this->dispatch('toast', message: 'Peran berhasil dihapus!', type: 'success');
        $this->loadRoles();
    }

    public function createUser(): void
    {
        $this->editingUser = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userRoleId = null;
        $this->resetValidation();
        $this->showUserModal = true;
    }

    public function editUser(User $user): void
    {
        $this->editingUser = $user;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userRoleId = null;
        $this->resetValidation();
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        if ($this->editingUser) {
            $this->validate([
                'userName' => 'required|string|max:255',
                'userEmail' => 'required|email|max:255|unique:users,email,' . $this->editingUser->id,
            ]);

            $this->editingUser->update([
                'name' => $this->userName,
                'email' => $this->userEmail,
            ]);

            $this->dispatch('toast', message: 'Pengguna berhasil diperbarui!', type: 'success');
        } else {
            $this->validate([
                'userName' => 'required|string|max:255',
                'userEmail' => 'required|email|max:255|unique:users,email',
                'userRoleId' => 'required|exists:roles,id',
            ]);

            $plainPassword = $this->generateDefaultPassword();

            $user = User::query()->create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => $plainPassword,
                'is_default_password' => true,
                'default_password_plain' => $plainPassword,
            ]);

            $role = Role::query()->findOrFail($this->userRoleId);
            $user->addRole($role);

            $this->dispatch('toast', message: 'Pengguna berhasil ditambahkan dengan password default baru.', type: 'success');
        }

        $this->showUserModal = false;
        unset($this->users, $this->totalUsers);
    }

    public function deleteUser(User $user): void
    {
        if ((int) auth()->id() === (int) $user->id) {
            $this->dispatch('toast', message: 'Tidak bisa menghapus akun yang sedang login.', type: 'error');

            return;
        }

        $user->delete();
        $this->dispatch('toast', message: 'Pengguna berhasil dihapus!', type: 'success');
        unset($this->users, $this->totalUsers);
    }

    public function resetPassword(User $user): void
    {
        $plainPassword = $this->generateDefaultPassword();

        $user->update([
            'password' => $plainPassword,
            'is_default_password' => true,
            'default_password_plain' => $plainPassword,
        ]);

        $this->dispatch('toast', message: 'Password berhasil direset.', type: 'success');
        unset($this->users, $this->totalUsers);
    }

    public function openChangeRole(User $user): void
    {
        if ($user->hasRole('alumni')) {
            $this->dispatch('toast', message: 'Peran alumni tidak dapat diubah melalui aksi ini.', type: 'error');

            return;
        }

        $this->changingRoleUser = $user;
        $this->newRoleId = $user->roles->first()?->id;
        $this->resetValidation();
        $this->showChangeRoleModal = true;
    }

    public function saveChangedRole(): void
    {
        $this->validate([
            'newRoleId' => 'required|exists:roles,id',
        ]);

        if (!$this->changingRoleUser) {
            return;
        }

        if ($this->changingRoleUser->hasRole('alumni')) {
            $this->dispatch('toast', message: 'Peran alumni tidak dapat diubah melalui aksi ini.', type: 'error');

            return;
        }

        foreach ($this->changingRoleUser->roles as $role) {
            $this->changingRoleUser->removeRole($role);
        }

        $newRole = Role::query()->findOrFail($this->newRoleId);
        $this->changingRoleUser->addRole($newRole);

        $this->showChangeRoleModal = false;
        $this->dispatch('toast', message: 'Peran pengguna berhasil diubah!', type: 'success');
        unset($this->users, $this->totalUsers);
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->resetValidation();
    }

    public function closeUserModal(): void
    {
        $this->showUserModal = false;
        $this->resetValidation();
    }

    public function closeChangeRoleModal(): void
    {
        $this->showChangeRoleModal = false;
        $this->resetValidation();
    }

    private function generateDefaultPassword(int $length = 8): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $password = '';

            for ($index = 0; $index < $length; $index++) {
                $password .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password));

        return $password;
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pengguna</h1>
            <p class="text-gray-600 mt-1">Kelola peran dan data pengguna dalam satu halaman</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="createRole" wire:loading.attr="disabled" wire:target="createRole"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-800 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="createRole">🛡️ Tambah Peran</span>
                <span wire:loading wire:target="createRole">Membuka...</span>
            </button>
            <button wire:click="createUser" wire:loading.attr="disabled" wire:target="createUser"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="createUser">➕ Tambah Pengguna</span>
                <span wire:loading wire:target="createUser">Membuka...</span>
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
        <h2 class="text-base font-semibold text-gray-900 mb-2">Daftar Peran</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
            @forelse ($roles as $role)
                <div wire:key="role-{{ $role->id }}" class="rounded-md border border-gray-200 bg-gray-50 p-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 leading-tight">
                            {{ $role->display_name ?: $role->name }}</p>
                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs whitespace-nowrap">
                        <button wire:click="editRole({{ $role->id }})" wire:loading.attr="disabled"
                            wire:target="editRole({{ $role->id }})"
                            class="text-blue-600 hover:text-blue-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="editRole({{ $role->id }})">Edit</span>
                            <span wire:loading wire:target="editRole({{ $role->id }})">Membuka...</span>
                        </button>
                        <button wire:click="deleteRole({{ $role->id }})"
                            class="text-red-600 hover:text-red-800 font-medium"
                            onclick="return confirm('Yakin hapus peran ini?')">Hapus</button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada data peran.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <input type="text" wire:model.live="search" placeholder="Cari pengguna (nama/email/peran)..."
            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Nama</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Peran</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Terakhir Login</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Password</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($this->users as $index => $user)
                    @php
                        $currentRole = $user->roles->first();
                        $isAlumniRole = $user->hasRole('alumni');
                    @endphp
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 text-sm text-gray-900 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $currentRole?->display_name ?: $currentRole?->name ?: '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $user->last_login_at ? $user->last_login_at->format('d-m-Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @if ($user->is_default_password && $user->default_password_plain)
                                <span
                                    class="font-mono bg-yellow-50 text-yellow-800 border border-yellow-200 px-2 py-1 rounded">
                                    {{ $user->default_password_plain }}
                                </span>
                            @else
                                <span class="text-green-700">Sudah diganti</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-right">
                            <div class="inline-flex max-w-67.5 flex-wrap justify-end gap-1.5">
                                <button wire:click="resetPassword({{ $user->id }})" wire:loading.attr="disabled"
                                    wire:target="resetPassword({{ $user->id }})"
                                    class="inline-flex items-center gap-1 rounded border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-60">
                                    <span wire:loading.remove
                                        wire:target="resetPassword({{ $user->id }})">Reset</span>
                                    <span wire:loading.inline-flex wire:target="resetPassword({{ $user->id }})"
                                        class="items-center gap-1">
                                        <svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Reset...
                                    </span>
                                </button>
                                <button wire:click="editUser({{ $user->id }})" wire:loading.attr="disabled"
                                    wire:target="editUser({{ $user->id }})"
                                    class="rounded border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="editUser({{ $user->id }})">Edit</span>
                                    <span wire:loading wire:target="editUser({{ $user->id }})">Membuka...</span>
                                </button>
                                <button wire:click="deleteUser({{ $user->id }})"
                                    class="rounded border border-red-200 bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100"
                                    onclick="return confirm('Yakin hapus pengguna ini?')">Hapus</button>
                                @if (!$isAlumniRole)
                                    <button wire:click="openChangeRole({{ $user->id }})"
                                        wire:loading.attr="disabled" wire:target="openChangeRole({{ $user->id }})"
                                        class="rounded border border-indigo-200 bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span wire:loading.remove
                                            wire:target="openChangeRole({{ $user->id }})">Ubah Peran</span>
                                        <span wire:loading
                                            wire:target="openChangeRole({{ $user->id }})">Membuka...</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada data pengguna</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($this->users->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold">{{ $this->users->count() }}</span> dari
                        <span class="font-semibold">{{ $this->totalUsers }}</span> total pengguna
                    </p>

                    @if ($this->users->count() < $this->totalUsers)
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

    @if ($this->users->count() > 0 && $this->users->count() < $this->totalUsers)
        <div wire:intersect="loadMore" class="h-10"></div>
    @endif

    @if ($showRoleModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeRoleModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ $editingRole ? 'Edit Peran' : 'Tambah Peran' }}
                    </h2>
                    <button wire:click="closeRoleModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveRole" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sistem</label>
                        <input type="text" wire:model.defer="roleName"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('roleName')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tampil</label>
                        <input type="text" wire:model.defer="roleDisplayName"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('roleDisplayName')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <input type="text" wire:model.defer="roleDescription"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('roleDescription')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeRoleModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-semibold transition shadow-sm">{{ $editingRole ? '💾 Simpan' : '➕ Tambah' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showUserModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeUserModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $editingUser ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h2>
                    <button wire:click="closeUserModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" wire:model.defer="userName"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('userName')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model.defer="userEmail"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        @error('userEmail')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @if (!$editingUser)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                            <select wire:model.defer="userRoleId"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Peran</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name ?: $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('userRoleId')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeUserModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-lg font-semibold transition shadow-sm">{{ $editingUser ? '💾 Simpan' : '➕ Tambah' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showChangeRoleModal)
        <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
            wire:click="closeChangeRoleModal">
            <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-6 animate-slideUp" wire:click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Ubah Peran Pengguna</h2>
                    <button wire:click="closeChangeRoleModal" class="text-gray-400 hover:text-gray-600">✕</button>
                </div>

                <form wire:submit="saveChangedRole" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peran Baru</label>
                        <select wire:model.defer="newRoleId"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih Peran</option>
                            @foreach ($roles as $role)
                                @if ($role->name !== 'alumni')
                                    <option value="{{ $role->id }}">{{ $role->display_name ?: $role->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('newRoleId')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeChangeRoleModal"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg font-semibold transition shadow-sm">🔄
                            Ubah Peran</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
