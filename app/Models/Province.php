<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk Provinsi di Indonesia
 */
class Province extends Model
{
    protected $table = 'provinces';

    protected $fillable = [
        'kode',
        'nama',
    ];

    /**
     * Relasi: Satu provinsi memiliki banyak kabupaten/kota
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
