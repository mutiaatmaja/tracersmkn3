<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use App\Models\University;
use Illuminate\Database\Seeder;

class UniversityStudyProgramSeeder extends Seeder
{
    /**
     * Seed data perguruan tinggi dan program studi dari kota besar di Indonesia.
     */
    public function run(): void
    {
        $universities = [
            [
                'kode' => 'UI',
                'nama' => 'Universitas Indonesia (Jakarta)',
                'programs' => [
                    ['kode' => 'UI-INF', 'nama' => 'Informatika'],
                    ['kode' => 'UI-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UI-AKN', 'nama' => 'Akuntansi'],
                ],
            ],
            [
                'kode' => 'UNJ',
                'nama' => 'Universitas Negeri Jakarta (Jakarta)',
                'programs' => [
                    ['kode' => 'UNJ-INF', 'nama' => 'Pendidikan Informatika'],
                    ['kode' => 'UNJ-EKO', 'nama' => 'Pendidikan Ekonomi'],
                    ['kode' => 'UNJ-ING', 'nama' => 'Pendidikan Bahasa Inggris'],
                ],
            ],
            [
                'kode' => 'ITB',
                'nama' => 'Institut Teknologi Bandung (Bandung)',
                'programs' => [
                    ['kode' => 'ITB-INF', 'nama' => 'Teknik Informatika'],
                    ['kode' => 'ITB-SIP', 'nama' => 'Sistem dan Teknologi Informasi'],
                    ['kode' => 'ITB-MNJ', 'nama' => 'Manajemen'],
                ],
            ],
            [
                'kode' => 'UPI',
                'nama' => 'Universitas Pendidikan Indonesia (Bandung)',
                'programs' => [
                    ['kode' => 'UPI-PTI', 'nama' => 'Pendidikan Teknologi Informasi'],
                    ['kode' => 'UPI-MTM', 'nama' => 'Pendidikan Matematika'],
                    ['kode' => 'UPI-MNJ', 'nama' => 'Manajemen'],
                ],
            ],
            [
                'kode' => 'ITS',
                'nama' => 'Institut Teknologi Sepuluh Nopember (Surabaya)',
                'programs' => [
                    ['kode' => 'ITS-INF', 'nama' => 'Teknik Informatika'],
                    ['kode' => 'ITS-SIF', 'nama' => 'Sistem Informasi'],
                    ['kode' => 'ITS-ELK', 'nama' => 'Teknik Elektro'],
                ],
            ],
            [
                'kode' => 'UNAIR',
                'nama' => 'Universitas Airlangga (Surabaya)',
                'programs' => [
                    ['kode' => 'UNAIR-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNAIR-AKN', 'nama' => 'Akuntansi'],
                    ['kode' => 'UNAIR-KES', 'nama' => 'Kesehatan Masyarakat'],
                ],
            ],
            [
                'kode' => 'UGM',
                'nama' => 'Universitas Gadjah Mada (Yogyakarta)',
                'programs' => [
                    ['kode' => 'UGM-ILK', 'nama' => 'Ilmu Komputer'],
                    ['kode' => 'UGM-HKM', 'nama' => 'Hukum'],
                    ['kode' => 'UGM-MNJ', 'nama' => 'Manajemen'],
                ],
            ],
            [
                'kode' => 'UNDIP',
                'nama' => 'Universitas Diponegoro (Semarang)',
                'programs' => [
                    ['kode' => 'UNDIP-INF', 'nama' => 'Informatika'],
                    ['kode' => 'UNDIP-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNDIP-AKN', 'nama' => 'Akuntansi'],
                ],
            ],
            [
                'kode' => 'USU',
                'nama' => 'Universitas Sumatera Utara (Medan)',
                'programs' => [
                    ['kode' => 'USU-INF', 'nama' => 'Teknologi Informasi'],
                    ['kode' => 'USU-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'USU-AKN', 'nama' => 'Akuntansi'],
                ],
            ],
            [
                'kode' => 'UNHAS',
                'nama' => 'Universitas Hasanuddin (Makassar)',
                'programs' => [
                    ['kode' => 'UNHAS-INF', 'nama' => 'Teknik Informatika'],
                    ['kode' => 'UNHAS-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNHAS-KES', 'nama' => 'Kesehatan Masyarakat'],
                ],
            ],
            [
                'kode' => 'UNSRI',
                'nama' => 'Universitas Sriwijaya (Palembang)',
                'programs' => [
                    ['kode' => 'UNSRI-INF', 'nama' => 'Sistem Komputer'],
                    ['kode' => 'UNSRI-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNSRI-AKN', 'nama' => 'Akuntansi'],
                ],
            ],
            [
                'kode' => 'UNUD',
                'nama' => 'Universitas Udayana (Denpasar)',
                'programs' => [
                    ['kode' => 'UNUD-INF', 'nama' => 'Teknologi Informasi'],
                    ['kode' => 'UNUD-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNUD-PAR', 'nama' => 'Pariwisata'],
                ],
            ],
            [
                'kode' => 'UNTAN',
                'nama' => 'Universitas Tanjungpura (Pontianak)',
                'programs' => [
                    ['kode' => 'UNTAN-INF', 'nama' => 'Informatika'],
                    ['kode' => 'UNTAN-MNJ', 'nama' => 'Manajemen'],
                    ['kode' => 'UNTAN-AKN', 'nama' => 'Akuntansi'],
                ],
            ],
            [
                'kode' => 'POLNEP',
                'nama' => 'Politeknik Negeri Pontianak (Pontianak)',
                'programs' => [
                    ['kode' => 'POLNEP-TI', 'nama' => 'Teknik Informatika'],
                    ['kode' => 'POLNEP-AKN', 'nama' => 'Akuntansi'],
                    ['kode' => 'POLNEP-ADM', 'nama' => 'Administrasi Bisnis'],
                ],
            ],
            [
                'kode' => 'IAINPTK',
                'nama' => 'IAIN Pontianak (Pontianak)',
                'programs' => [
                    ['kode' => 'IAIN-PAI', 'nama' => 'Pendidikan Agama Islam'],
                    ['kode' => 'IAIN-ESY', 'nama' => 'Ekonomi Syariah'],
                    ['kode' => 'IAIN-PBA', 'nama' => 'Pendidikan Bahasa Arab'],
                ],
            ],
            [
                'kode' => 'UMPONTI',
                'nama' => 'Universitas Muhammadiyah Pontianak (Pontianak)',
                'programs' => [
                    ['kode' => 'UMP-TI', 'nama' => 'Teknik Informatika'],
                    ['kode' => 'UMP-KES', 'nama' => 'Kesehatan Masyarakat'],
                    ['kode' => 'UMP-MNJ', 'nama' => 'Manajemen'],
                ],
            ],
        ];

        foreach ($universities as $universityData) {
            $university = University::updateOrCreate(
                ['kode' => $universityData['kode']],
                ['nama' => $universityData['nama']]
            );

            foreach ($universityData['programs'] as $programData) {
                StudyProgram::updateOrCreate(
                    ['kode' => $programData['kode']],
                    [
                        'university_id' => $university->id,
                        'nama' => $programData['nama'],
                    ]
                );
            }
        }
    }
}
