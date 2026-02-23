<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed daftar negara utama.
     */
    public function run(): void
    {
        $countries = [
            ['kode' => 'ID', 'nama' => 'Indonesia'],
            ['kode' => 'US', 'nama' => 'Amerika Serikat'],
            ['kode' => 'CN', 'nama' => 'China'],
            ['kode' => 'JP', 'nama' => 'Jepang'],
            ['kode' => 'IN', 'nama' => 'India'],
            ['kode' => 'DE', 'nama' => 'Jerman'],
            ['kode' => 'GB', 'nama' => 'Inggris'],
            ['kode' => 'AU', 'nama' => 'Australia'],
            ['kode' => 'SG', 'nama' => 'Singapura'],
            ['kode' => 'MY', 'nama' => 'Malaysia'],
            ['kode' => 'KR', 'nama' => 'Korea Selatan'],
            ['kode' => 'AE', 'nama' => 'Uni Emirat Arab'],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(
                ['kode' => $country['kode']],
                ['nama' => $country['nama']],
            );
        }
    }
}
