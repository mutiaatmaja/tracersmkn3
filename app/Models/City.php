<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk Kabupaten/Kota di Indonesia
 */
class City extends Model
{
    protected $table = 'cities';

    protected $fillable = [
        'province_id',
        'kode',
        'nama',
        'tipe',
    ];

    /**
     * Relasi: Kota/kabupaten belongs to provinsi
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
