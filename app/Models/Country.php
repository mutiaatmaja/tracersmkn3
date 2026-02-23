<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk daftar negara.
 */
class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
