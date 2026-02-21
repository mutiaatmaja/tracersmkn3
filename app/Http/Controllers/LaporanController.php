<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class LaporanController extends Controller
{
    public function alumniPdf(Request $request)
    {
        $tahunLulusFilter = $request->string('tahun_lulus')->toString();

        $query = Alumni::query();

        if ($tahunLulusFilter !== '') {
            $query->where('tahun_lulus', (int) $tahunLulusFilter);
        }

        $alumniPerTahun = (clone $query)
            ->selectRaw('tahun_lulus, COUNT(*) as total')
            ->whereNotNull('tahun_lulus')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->get();

        $totalAlumni = (clone $query)->count();
        $sudahKlaim = (clone $query)->where('is_claimed', true)->count();
        $belumKlaim = $totalAlumni - $sudahKlaim;

        $statistikJenisKelamin = (clone $query)
            ->selectRaw('jenis_kelamin, COUNT(*) as total')
            ->whereNotNull('jenis_kelamin')
            ->groupBy('jenis_kelamin')
            ->orderBy('jenis_kelamin')
            ->get();

        $alumniDenganTanggalLahir = (clone $query)
            ->whereNotNull('tanggal_lahir')
            ->get(['tanggal_lahir']);

        $usiaList = $alumniDenganTanggalLahir
            ->map(fn ($alumni) => now()->diffInYears($alumni->tanggal_lahir))
            ->values();

        $bucketUsia = [
            '< 20 tahun' => 0,
            '20 - 24 tahun' => 0,
            '25 - 29 tahun' => 0,
            '>= 30 tahun' => 0,
        ];

        foreach ($usiaList as $usia) {
            if ($usia < 20) {
                $bucketUsia['< 20 tahun']++;

                continue;
            }

            if ($usia <= 24) {
                $bucketUsia['20 - 24 tahun']++;

                continue;
            }

            if ($usia <= 29) {
                $bucketUsia['25 - 29 tahun']++;

                continue;
            }

            $bucketUsia['>= 30 tahun']++;
        }

        $statistikUmur = [
            'rata_rata' => $usiaList->isNotEmpty() ? round($usiaList->avg(), 1) : 0,
            'total_terdata' => $usiaList->count(),
            'total_tidak_terdata' => (clone $query)->whereNull('tanggal_lahir')->count(),
            'bucket' => $bucketUsia,
        ];

        return Pdf::view('reports.alumni-statistics', [
            'alumniPerTahun' => $alumniPerTahun,
            'totalAlumni' => $totalAlumni,
            'sudahKlaim' => $sudahKlaim,
            'belumKlaim' => $belumKlaim,
            'statistikJenisKelamin' => $statistikJenisKelamin,
            'statistikUmur' => $statistikUmur,
            'tahunLulusFilter' => $tahunLulusFilter !== '' ? (int) $tahunLulusFilter : null,
        ])
            ->format('a4')
            ->name('laporan-statistik-alumni.pdf')
            ->download();
    }
}
