<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

/**
 * Model untuk User / Pengguna Aplikasi
 *
 * Mencakup Admin, Guru, Staff, Kepala Sekolah, dan Alumni
 * User untuk autentikasi, sedangkan data spesifik alumni ada di tabel alumni terpisah
 */
class User extends Authenticatable implements LaratrustUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRolesAndPermissions, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (mass assignment)
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'is_default_password',
        'default_password_plain',
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data untuk field tertentu
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_default_password' => 'boolean',
        ];
    }

    /**
     * Relasi: User bisa memiliki satu data Alumni (jika user tersebut alumni)
     *
     * Tidak semua user adalah alumni - admin, guru, kepsek dll tidak punya data alumni
     */
    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class);
    }

    /**
     * Cek apakah user adalah alumni
     */
    public function isAlumni(): bool
    {
        return $this->hasRole('alumni') && $this->alumni()->exists();
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Cek apakah user adalah guru
     */
    public function isGuru(): bool
    {
        return $this->hasRole('guru');
    }
}
