<?php

namespace App\Imports;

use App\Models\Alumni;
use App\Models\Competency;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlumnisImport implements ToCollection, WithHeadingRow
{
    public int $createdCount = 0;

    public int $updatedCount = 0;

    public int $skippedCount = 0;

    public function collection(Collection $rows): void
    {
        $competenciesByCode = Competency::query()
            ->get()
            ->mapWithKeys(function (Competency $competency) {
                return [strtoupper(trim($competency->kode)) => $competency->id];
            });

        foreach ($rows as $row) {
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? $row['nama'] ?? ''));
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $nik = trim((string) ($row['nik'] ?? ''));
            $kompetensiKode = strtoupper(trim((string) ($row['kompetensi_kode'] ?? $row['competency_kode'] ?? '')));
            $tahunLulus = (int) ($row['tahun_lulus'] ?? 0);
            $jenisKelamin = strtolower(trim((string) ($row['jenis_kelamin'] ?? '')));

            if ($namaLengkap === '' || ($nisn === '' && $nik === '') || $kompetensiKode === '' || $tahunLulus < 1900) {
                $this->skippedCount++;

                continue;
            }

            if (! in_array($jenisKelamin, ['laki-laki', 'perempuan'], true)) {
                $this->skippedCount++;

                continue;
            }

            $competencyId = $competenciesByCode->get($kompetensiKode);

            if (! $competencyId) {
                $this->skippedCount++;

                continue;
            }

            $alumniQuery = Alumni::query();

            if ($nisn !== '' && $nik !== '') {
                $alumniQuery->where('nisn', $nisn)->orWhere('nik', $nik);
            } elseif ($nisn !== '') {
                $alumniQuery->where('nisn', $nisn);
            } else {
                $alumniQuery->where('nik', $nik);
            }

            $existingAlumni = $alumniQuery->first();

            $payload = [
                'nama_lengkap' => $namaLengkap,
                'nisn' => $nisn !== '' ? $nisn : null,
                'nik' => $nik !== '' ? $nik : null,
                'competency_id' => $competencyId,
                'tahun_lulus' => $tahunLulus,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $this->nullableString($row['tempat_lahir'] ?? null),
                'tanggal_lahir' => $this->nullableString($row['tanggal_lahir'] ?? null),
                'nomor_telepon' => $this->nullableString($row['nomor_telepon'] ?? $row['no_hp'] ?? null),
                'alamat' => $this->nullableString($row['alamat'] ?? null),
            ];

            if ($existingAlumni) {
                $existingAlumni->update($payload);
                $this->updatedCount++;

                continue;
            }

            Alumni::query()->create($payload);
            $this->createdCount++;
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $stringValue = trim((string) $value);

        return $stringValue !== '' ? $stringValue : null;
    }
}
