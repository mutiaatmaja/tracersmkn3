<?php

namespace App\Exports;

use App\Models\Competency;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlumnisDummyExport implements FromArray, WithHeadings
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
        $competencyCodes = Competency::query()->pluck('kode')->map(fn ($code) => strtoupper((string) $code))->values()->all();

        if (count($competencyCodes) === 0) {
            $competencyCodes = ['RPL'];
        }

        return [
            ['Andi Pratama', '0091110001', '6171010101010101', $competencyCodes[0], now()->year - 1, 'laki-laki', 'Pontianak', '2005-02-11', '081230000001', 'Jl. Ahmad Yani No. 11'],
            ['Siti Rahma', '0091110002', '6171010101010102', $competencyCodes[1 % count($competencyCodes)], now()->year - 1, 'perempuan', 'Pontianak', '2005-06-22', '081230000002', 'Jl. Purnama No. 5'],
            ['Beni Saputra', '0091110003', '6171010101010103', $competencyCodes[2 % count($competencyCodes)], now()->year - 2, 'laki-laki', 'Kubu Raya', '2004-08-10', '081230000003', 'Jl. Adisucipto No. 21'],
            ['Rina Marlina', '0091110004', '6171010101010104', $competencyCodes[0], now()->year - 2, 'perempuan', 'Mempawah', '2004-12-05', '081230000004', 'Jl. Gusti Situt Mahmud No. 9'],
            ['Fajar Hidayat', '0091110005', '6171010101010105', $competencyCodes[1 % count($competencyCodes)], now()->year - 3, 'laki-laki', 'Sungai Raya', '2003-03-17', '081230000005', 'Jl. Arteri Supadio No. 18'],
        ];
    }
}
