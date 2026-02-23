<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProvinceSeeder extends Seeder
{
    /**
     * Seed data provinsi dan kabupaten/kota di Indonesia dari file CSV.
     */
    public function run(): void
    {
        $provinceIdsByCode = $this->seedProvincesFromCsv();

        $this->seedCitiesFromCsv($provinceIdsByCode);
    }

    /**
     * Seed provinsi dari CSV dan kembalikan mapping kode provinsi => id.
     *
     * @return array<string, int>
     */
    private function seedProvincesFromCsv(): array
    {
        $rows = $this->readCsvRows(public_path('indonesia/indonesia_provinces.csv'));
        $provinceIdsByCode = [];

        foreach ($rows as $row) {
            $code = trim((string) ($row['code'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));

            if ($code === '' || $name === '') {
                continue;
            }

            $province = Province::query()->updateOrCreate(
                ['kode' => $code],
                ['nama' => $name],
            );

            $provinceIdsByCode[$code] = $province->id;
        }

        return $provinceIdsByCode;
    }

    /**
     * Seed kabupaten/kota dari CSV.
     *
     * @param  array<string, int>  $provinceIdsByCode
     */
    private function seedCitiesFromCsv(array $provinceIdsByCode): void
    {
        $rows = $this->readCsvRows(public_path('indonesia/indonesia_cities.csv'));

        foreach ($rows as $row) {
            $code = trim((string) ($row['code'] ?? ''));
            $provinceCode = trim((string) ($row['province_code'] ?? ''));
            $name = trim((string) ($row['name'] ?? ''));

            if ($code === '' || $provinceCode === '' || $name === '') {
                continue;
            }

            $provinceId = $provinceIdsByCode[$provinceCode] ?? null;

            if (! $provinceId) {
                continue;
            }

            City::query()->updateOrCreate(
                ['kode' => $code],
                [
                    'province_id' => $provinceId,
                    'nama' => $name,
                    'tipe' => $this->resolveCityType($name),
                ],
            );
        }
    }

    /**
     * Baca file CSV delimiter ';' menjadi array asosiatif berdasarkan header.
     *
     * @return array<int, array<string, string>>
     */
    private function readCsvRows(string $path): array
    {
        if (! is_readable($path)) {
            throw new RuntimeException("CSV tidak ditemukan atau tidak bisa dibaca: {$path}");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Gagal membuka file CSV: {$path}");
        }

        $header = fgetcsv($handle, 0, ';');

        if ($header === false) {
            fclose($handle);

            return [];
        }

        $header = array_map(fn ($value) => trim((string) $value, "\" \t\n\r\0\x0B"), $header);
        $rows = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) !== count($header)) {
                continue;
            }

            $cleanValues = array_map(fn ($value) => trim((string) $value, "\" \t\n\r\0\x0B"), $data);

            /** @var array<string, string> $row */
            $row = array_combine($header, $cleanValues);

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Tentukan tipe wilayah dari nama kota/kabupaten.
     */
    private function resolveCityType(string $name): string
    {
        $upper = strtoupper($name);

        if (str_starts_with($upper, 'KABUPATEN')) {
            return 'kabupaten';
        }

        return 'kota';
    }
}
