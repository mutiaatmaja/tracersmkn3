<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk Data Alumni
 *
 * Menyimpan data lengkap alumni termasuk NISN, jurusan, tahun lulus, dll
 * Terpisah dari tabel users karena:
 * - Users table untuk autentikasi (admin, guru, alumni, dll)
 * - Alumni table khusus untuk data alumni
 */
class Alumni extends Model
{
    // Nama tabel di database
    protected $table = 'alumnis';

    /**
     * Field yang dapat diisi secara massal (mass assignment)
     */
    protected $fillable = [
        'user_id',
        'nisn',
        'nik',
        'competency_id',
        'tahun_lulus',
        'jenis_kelamin',
        'last_tracer_date',
        'next_tracer_eligible_date',
        'foto_profil',
        'link_media_sosial',
    ];

    /**
     * Cast tipe data untuk field tertentu
     */
    protected $casts = [
        'tahun_lulus' => 'integer',
        'last_tracer_date' => 'date',
        'next_tracer_eligible_date' => 'date',
    ];

    /**
     * Relasi: Alumni belongs to User (satu alumni satu user)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Alumni belongs to Competency
     */
    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    /**
     * Cek apakah alumni sudah bisa mengisi tracer baru
     */
    public function canFillTracer(): bool
    {
        return $this->next_tracer_eligible_date === null ||
               now()->greaterThanOrEqualTo($this->next_tracer_eligible_date);
    }

    /**
     * Update data tracer terakhir dan tentukan kapan bisa isi lagi (1 tahun kemudian)
     */
    public function markTracerFilled(): void
    {
        $this->update([
            'last_tracer_date' => now()->toDateString(),
            'next_tracer_eligible_date' => now()->addYear()->toDateString(),
        ]);
    }

    /**
     * Scope untuk mendapatkan alumni yang bisa mengisi tracer
     */
    public function scopeCanFillTracer($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('next_tracer_eligible_date')
                ->orWhere('next_tracer_eligible_date', '<=', now());
        });
    }
}
