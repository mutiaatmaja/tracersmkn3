<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * yang dilakukan di sini adalah kita membuat role dan user sekaligus, jadi kita buat array dulu untuk menyimpan data role yang akan dibuat, lalu kita loop berdasarkan data di atas, untuk membuat role dan user sekaligus, nah kita buat user sebanyak 2 untuk setiap role, jadi kita buat loop lagi untuk membuat user sebanyak 2
     * jadi nanti akan ada 2 user untuk setiap role, dengan email yang berbeda-beda, dan password yang sama yaitu 'password'
     */
    public function run(): void
    {
        //berkut kita buat role dan user sekaligus, jadi kita buat array dulu untuk menyimpan data role yang akan dibuat
        $roles = [
            'admin' => 'Admin',
            'tu' => 'TU',
            'guru' => 'Guru',
            'waka' => 'Waka',
            'kepalasekolah' => 'Kepala Sekolah',
            'alumni' => 'Alumni',
            'siswa' => 'Siswa',
        ];
        //kita loop dulu berdasarkan data di atas, untuk membuat role dan user sekaligus
        foreach ($roles as $name => $label) {
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $label]
            );
            //nah kita buat user sebanyak 2 untuk setiap role, jadi kita buat loop lagi untuk membuat user sebanyak 2
            for ($index = 1; $index <= 2; $index++) {
                $user = User::factory()->create([
                    'name' => $label . ' User ' . $index,
                    'email' => $name . $index . '@smkn3ptk.test',
                    'password' => bcrypt('password'),
                ]);

                $user->addRole($role);
            }
        }
    }
}
