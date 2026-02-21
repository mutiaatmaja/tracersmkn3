<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Jalankan dengan: php artisan db:seed
 * Membuat data awal untuk testing dan development
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Jalankan seeders untuk membuat data awal
     */
    public function run(): void
    {
        // Panggil seeder untuk kompetensi
        $this->call(CompetencySeeder::class);

        // Panggil seeder perguruan tinggi dan program studi
        $this->call(UniversityStudyProgramSeeder::class);

        // Buat data setting awal untuk informasi sekolah
        $this->seedSettings();

        // Buat data role dan user awal
        $this->seedRolesAndUsers();
    }

    /**
     * Buat data pengaturan awal
     */
    private function seedSettings(): void
    {
        $settings = [
            'school_name' => 'SMKN 3 Pontianak',
            'school_address' => 'Jl. Jend. A. Yani, Pontianak, Kalimantan Barat',
            'school_phone' => '(0561) 123456',
            'school_email' => 'smkn3ptk@example.com',
            'principal_name' => 'Drs. H. Suparman, M.Pd.',
            'principal_contact' => '(0561) 234567',
            'website' => 'https://smkn3pontianak.sch.id',
            'tracer_frequency' => 'yearly',
            'tracer_month' => '3',
            'tracer_duration_days' => '90',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Buat data role dan user awal
     *
     * Membuat 7 role dengan masing-masing 2 user
     */
    private function seedRolesAndUsers(): void
    {
        // Data role yang akan dibuat
        $roles = [
            'admin' => 'Admin',
            'tu' => 'Tata Usaha',
            'guru' => 'Guru',
            'waka' => 'Wakil Kepala Sekolah',
            'kepalasekolah' => 'Kepala Sekolah',
            'alumni' => 'Alumni',
            'siswa' => 'Siswa',
        ];

        // Loop untuk membuat role dan user
        foreach ($roles as $name => $label) {
            // Buat atau ambil role yang sudah ada
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $label]
            );

            // Buat 2 user untuk setiap role
            for ($index = 1; $index <= 2; $index++) {
                $user = User::factory()->create([
                    'name' => $label.' User '.$index,
                    'email' => $name.$index.'@smkn3ptk.test',
                    'password' => bcrypt('password'),
                    'is_default_password' => true,
                    'default_password_plain' => 'password',
                ]);

                // Assign role ke user
                $user->addRole($role);
            }
        }
    }
}
