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

    /**
     * @var array<int, array{row:int, reason:string}>
     */
    public array $skippedDetails = [];

    public function collection(Collection $rows): void
    {
        $competenciesByCode = Competency::query()
            ->get()
            ->mapWithKeys(function (Competency $competency) {
                return [strtoupper(trim($competency->kode)) => $competency->id];
            });

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $namaLengkap = trim((string) ($row['nama_lengkap'] ?? $row['nama'] ?? ''));
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $nik = trim((string) ($row['nik'] ?? ''));
            $kompetensiKode = strtoupper(trim((string) ($row['kompetensi_kode'] ?? $row['competency_kode'] ?? '')));
            $tahunLulus = (int) ($row['tahun_lulus'] ?? 0);
            $jenisKelamin = strtolower(trim((string) ($row['jenis_kelamin'] ?? '')));

            if ($namaLengkap === '') {
                $this->skipRow($rowNumber, 'nama lengkap kosong.');

                continue;
            }

            if ($nisn === '' && $nik === '') {
                $this->skipRow($rowNumber, 'NISN dan NIK kosong. Salah satu wajib diisi.');

                continue;
            }

            if ($kompetensiKode === '') {
                $this->skipRow($rowNumber, 'kode kompetensi kosong.');

                continue;
            }

            if ($tahunLulus < 1900 || $tahunLulus > now()->year + 1) {
                $this->skipRow($rowNumber, 'tahun lulus tidak valid.');

                continue;
            }

            if (! in_array($jenisKelamin, ['laki-laki', 'perempuan'], true)) {
                $this->skipRow($rowNumber, 'jenis kelamin harus laki-laki atau perempuan.');

                continue;
            }

            $competencyId = $competenciesByCode->get($kompetensiKode);

            if (! $competencyId) {
                $this->skipRow($rowNumber, "kode kompetensi '{$kompetensiKode}' tidak ditemukan.");

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

    private function skipRow(int $rowNumber, string $reason): void
    {
        $this->skippedCount++;

        $this->skippedDetails[] = [
            'row' => $rowNumber,
            'reason' => $reason,
        ];
    }
}
