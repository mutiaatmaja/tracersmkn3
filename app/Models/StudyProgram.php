<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyProgram extends Model
{
    protected $table = 'study_programs';

    protected $fillable = [
        'university_id',
        'kode',
        'nama',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }
}
