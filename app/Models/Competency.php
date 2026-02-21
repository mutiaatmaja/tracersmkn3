<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk Kompetensi Keahlian / Jurusan
 *
 * Menyimpan data kompetensi keahlian yang ada di sekolah
 * Contoh: RPL (Rekayasa Perangkat Lunak), TKJ (Teknik Komputer Jaringan), dll
 */
class Competency extends Model
{
    // Nama tabel di database
    protected $table = 'competencies';

    /**
     * Field yang dapat diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'aktif',
    ];

    /**
     * Cast tipe data untuk field tertentu
     */
    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Relasi: Satu kompetensi memiliki banyak alumni
     */
    public function alumnis(): HasMany
    {
        return $this->hasMany(Alumni::class);
    }

    /**
     * Scope untuk mendapatkan hanya kompetensi yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}
