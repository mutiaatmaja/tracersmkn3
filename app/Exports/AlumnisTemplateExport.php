<?php

namespace App\Exports;

use App\Models\Competency;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlumnisTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'nama_lengkap',
            'nisn',
            'nik',
            'kompetensi_kode',
            'tahun_lulus',
            'jenis_kelamin',
            'tempat_lahir',
            'tanggal_lahir',
            'nomor_telepon',
            'alamat',
        ];
    }

    public function array(): array
    {
        $defaultCompetencyCode = Competency::query()->value('kode') ?? 'RPL';

        return [[
            'Budi Santoso',
            '0099988877',
            '6171010101010001',
            strtoupper($defaultCompetencyCode),
            now()->year - 1,
            'laki-laki',
            'Pontianak',
            '2006-01-01',
            '081234567890',
            'Jl. Contoh No. 1',
        ]];
    }
}
