<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Seed data provinsi dan kabupaten/kota di Indonesia
     */
    public function run(): void
    {
        $provinces = [
            [
                'kode' => '61',
                'nama' => 'Kalimantan Barat',
                'cities' => [
                    ['kode' => '6101', 'nama' => 'Sambas', 'tipe' => 'kabupaten'],
                    ['kode' => '6102', 'nama' => 'Bengkayang', 'tipe' => 'kabupaten'],
                    ['kode' => '6103', 'nama' => 'Landak', 'tipe' => 'kabupaten'],
                    ['kode' => '6104', 'nama' => 'Pontianak', 'tipe' => 'kabupaten'],
                    ['kode' => '6105', 'nama' => 'Sanggau', 'tipe' => 'kabupaten'],
                    ['kode' => '6106', 'nama' => 'Ketapang', 'tipe' => 'kabupaten'],
                    ['kode' => '6107', 'nama' => 'Sintang', 'tipe' => 'kabupaten'],
                    ['kode' => '6108', 'nama' => 'Kapuas Hulu', 'tipe' => 'kabupaten'],
                    ['kode' => '6109', 'nama' => 'Sekadau', 'tipe' => 'kabupaten'],
                    ['kode' => '6110', 'nama' => 'Melawi', 'tipe' => 'kabupaten'],
                    ['kode' => '6111', 'nama' => 'Kayong Utara', 'tipe' => 'kabupaten'],
                    ['kode' => '6112', 'nama' => 'Kubu Raya', 'tipe' => 'kabupaten'],
                    ['kode' => '6171', 'nama' => 'Pontianak', 'tipe' => 'kota'],
                    ['kode' => '6172', 'nama' => 'Singkawang', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '11',
                'nama' => 'Aceh',
                'cities' => [
                    ['kode' => '1101', 'nama' => 'Simeulue', 'tipe' => 'kabupaten'],
                    ['kode' => '1102', 'nama' => 'Aceh Singkil', 'tipe' => 'kabupaten'],
                    ['kode' => '1171', 'nama' => 'Banda Aceh', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '12',
                'nama' => 'Sumatera Utara',
                'cities' => [
                    ['kode' => '1201', 'nama' => 'Nias', 'tipe' => 'kabupaten'],
                    ['kode' => '1271', 'nama' => 'Medan', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '31',
                'nama' => 'DKI Jakarta',
                'cities' => [
                    ['kode' => '3171', 'nama' => 'Jakarta Pusat', 'tipe' => 'kota'],
                    ['kode' => '3172', 'nama' => 'Jakarta Utara', 'tipe' => 'kota'],
                    ['kode' => '3173', 'nama' => 'Jakarta Barat', 'tipe' => 'kota'],
                    ['kode' => '3174', 'nama' => 'Jakarta Selatan', 'tipe' => 'kota'],
                    ['kode' => '3175', 'nama' => 'Jakarta Timur', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '32',
                'nama' => 'Jawa Barat',
                'cities' => [
                    ['kode' => '3201', 'nama' => 'Bogor', 'tipe' => 'kabupaten'],
                    ['kode' => '3204', 'nama' => 'Bandung', 'tipe' => 'kabupaten'],
                    ['kode' => '3271', 'nama' => 'Bandung', 'tipe' => 'kota'],
                    ['kode' => '3273', 'nama' => 'Bekasi', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '33',
                'nama' => 'Jawa Tengah',
                'cities' => [
                    ['kode' => '3371', 'nama' => 'Semarang', 'tipe' => 'kota'],
                    ['kode' => '3372', 'nama' => 'Surakarta', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '34',
                'nama' => 'DI Yogyakarta',
                'cities' => [
                    ['kode' => '3471', 'nama' => 'Yogyakarta', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '35',
                'nama' => 'Jawa Timur',
                'cities' => [
                    ['kode' => '3571', 'nama' => 'Surabaya', 'tipe' => 'kota'],
                    ['kode' => '3572', 'nama' => 'Malang', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '51',
                'nama' => 'Bali',
                'cities' => [
                    ['kode' => '5171', 'nama' => 'Denpasar', 'tipe' => 'kota'],
                ],
            ],
            [
                'kode' => '73',
                'nama' => 'Sulawesi Selatan',
                'cities' => [
                    ['kode' => '7371', 'nama' => 'Makassar', 'tipe' => 'kota'],
                ],
            ],
        ];

        foreach ($provinces as $provinceData) {
            $province = Province::create([
                'kode' => $provinceData['kode'],
                'nama' => $provinceData['nama'],
            ]);

            foreach ($provinceData['cities'] as $cityData) {
                City::create([
                    'province_id' => $province->id,
                    'kode' => $cityData['kode'],
                    'nama' => $cityData['nama'],
                    'tipe' => $cityData['tipe'],
                ]);
            }
        }
    }
}
