<?php

namespace Database\Seeders;

use App\Models\Competency;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk data Kompetensi Keahlian / Jurusan
 *
 * Dipanggil saat php artisan db:seed
 * Membuat data awal kompetensi yang ada di SMKN 3 Pontianak
 */
class CompetencySeeder extends Seeder
{
    /**
     * Jalankan seeder
     */
    public function run(): void
    {
        // Data kompetensi keahlian SMKN 3 Pontianak
        $competencies = [
            [
                'kode' => 'RPL',
                'nama' => 'Rekayasa Perangkat Lunak',
                'deskripsi' => 'Kompetensi untuk mengembangkan aplikasi dan software',
                'aktif' => true,
            ],
            [
                'kode' => 'TKJ',
                'nama' => 'Teknik Komputer Jaringan',
                'deskripsi' => 'Kompetensi untuk mengelola infrastruktur jaringan komputer',
                'aktif' => true,
            ],
            [
                'kode' => 'AKL',
                'nama' => 'Akuntansi Keuangan Lembaga',
                'deskripsi' => 'Kompetensi untuk mengelola keuangan dan akuntansi',
                'aktif' => true,
            ],
            [
                'kode' => 'BDP',
                'nama' => 'Bisnis Daring dan Pemasaran',
                'deskripsi' => 'Kompetensi untuk mengelola bisnis online dan digital marketing',
                'aktif' => true,
            ],
            [
                'kode' => 'OTKP',
                'nama' => 'Otomasi Tata Kelola Perkantoran',
                'deskripsi' => 'Kompetensi untuk mengelola perkantoran secara digital',
                'aktif' => true,
            ],
            [
                'kode' => 'DKV',
                'nama' => 'Desain Komunikasi Visual',
                'deskripsi' => 'Kompetensi untuk mendesain visual dan multimedia',
                'aktif' => true,
            ],
        ];

        // Insert semua data kompetensi
        foreach ($competencies as $competency) {
            Competency::firstOrCreate(
                ['kode' => $competency['kode']],
                $competency
            );
        }
    }
}
