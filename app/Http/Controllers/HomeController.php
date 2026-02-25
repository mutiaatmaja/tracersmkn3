<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\TracerSubmission;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     */
    public function index(): Renderable
    {
        $currentUser = auth()->user();
        $isAlumniUser = $currentUser?->hasRole('alumni') && $currentUser->alumni()->exists();

        $totalAlumni = Alumni::query()->count();

        $alumniPerTahun = Alumni::query()
            ->selectRaw('tahun_lulus, COUNT(*) as total')
            ->whereNotNull('tahun_lulus')
            ->groupBy('tahun_lulus')
            ->orderByDesc('tahun_lulus')
            ->limit(6)
            ->get();

        $latestPeriodeTracer = TracerSubmission::query()->max('periode_tahun');

        $submittedTracerQuery = TracerSubmission::query()->where('status', 'submitted');

        $jumlahPengisiTracer = (clone $submittedTracerQuery)
            ->distinct('alumni_id')
            ->count('alumni_id');

        $jumlahPengisiTracerPeriodeAktif = $latestPeriodeTracer
            ? (clone $submittedTracerQuery)->where('periode_tahun', $latestPeriodeTracer)->count()
            : 0;

        $persentasePengisiTracer = $totalAlumni > 0
            ? round(($jumlahPengisiTracer / $totalAlumni) * 100, 1)
            : 0;

        $jumlahBekerja = (clone $submittedTracerQuery)->where('b2_bekerja', true)->count();
        $jumlahStudiLanjut = (clone $submittedTracerQuery)->where('b1_studi_lanjut', true)->count();
        $jumlahWirausaha = (clone $submittedTracerQuery)
            ->whereIn('b3_bentuk_pekerjaan', [
                'wirausaha_tanpa_pekerja',
                'wirausaha_pekerja_tidak_dibayar',
                'wirausaha_pekerja_dibayar',
            ])
            ->count();

        $jenisInstansiStats = (clone $submittedTracerQuery)
            ->selectRaw('c8_jenis_instansi, COUNT(*) as total')
            ->whereNotNull('c8_jenis_instansi')
            ->where('c8_jenis_instansi', '!=', '')
            ->groupBy('c8_jenis_instansi')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $salaryDistribution = (clone $submittedTracerQuery)
            ->selectRaw('c10_penghasilan_bulanan, COUNT(*) as total')
            ->whereNotNull('c10_penghasilan_bulanan')
            ->where('c10_penghasilan_bulanan', '!=', '')
            ->groupBy('c10_penghasilan_bulanan')
            ->get();

        $salaryMap = [
            'kurang_3_juta' => 2500000,
            '3_5_juta' => 4000000,
            'lebih_5_juta' => 6000000,
        ];

        $jumlahResponBergaji = $salaryDistribution->sum(function ($row) use ($salaryMap) {
            return isset($salaryMap[$row->c10_penghasilan_bulanan]) ? (int) $row->total : 0;
        });

        $totalAkumulasiGaji = $salaryDistribution->sum(function ($row) use ($salaryMap) {
            return ($salaryMap[$row->c10_penghasilan_bulanan] ?? 0) * (int) $row->total;
        });

        $averageGaji = $jumlahResponBergaji > 0
            ? (int) round($totalAkumulasiGaji / $jumlahResponBergaji)
            : null;

        $keselarasanPekerjaan = (clone $submittedTracerQuery)
            ->selectRaw('c14_kesesuaian_pekerjaan, COUNT(*) as total')
            ->whereNotNull('c14_kesesuaian_pekerjaan')
            ->where('c14_kesesuaian_pekerjaan', '!=', '')
            ->groupBy('c14_kesesuaian_pekerjaan')
            ->orderByDesc('total')
            ->get();

        $keselarasanStudi = (clone $submittedTracerQuery)
            ->selectRaw('d5_kesesuaian_studi, COUNT(*) as total')
            ->whereNotNull('d5_kesesuaian_studi')
            ->where('d5_kesesuaian_studi', '!=', '')
            ->groupBy('d5_kesesuaian_studi')
            ->orderByDesc('total')
            ->get();

        $alumniTracerHistory = collect();
        $alumniTracerTahunIniStatus = 'belum_isi';
        $alumniTracerTahunIni = null;
        $alumniBisaIsiLagiPada = null;
        $alumniCanFillTracer = false;

        if ($isAlumniUser) {
            $alumni = $currentUser->alumni;

            /** @var Collection<int, TracerSubmission> $alumniTracerHistory */
            $alumniTracerHistory = TracerSubmission::query()
                ->where('alumni_id', $alumni->id)
                ->orderByDesc('periode_tahun')
                ->orderByDesc('updated_at')
                ->get();

            $alumniTracerTahunIni = $alumniTracerHistory
                ->firstWhere('periode_tahun', now()->year);

            if ($alumniTracerTahunIni) {
                $alumniTracerTahunIniStatus = $alumniTracerTahunIni->status === 'submitted'
                    ? 'sudah_isi'
                    : 'draft';
            }

            $alumniBisaIsiLagiPada = $alumni->next_tracer_eligible_date;
            $alumniCanFillTracer = $alumni->canFillTracer();
        }

        return view('home', [
            'isAlumniUser' => $isAlumniUser,
            'totalAlumni' => $totalAlumni,
            'alumniPerTahun' => $alumniPerTahun,
            'latestPeriodeTracer' => $latestPeriodeTracer,
            'jumlahPengisiTracer' => $jumlahPengisiTracer,
            'jumlahPengisiTracerPeriodeAktif' => $jumlahPengisiTracerPeriodeAktif,
            'persentasePengisiTracer' => $persentasePengisiTracer,
            'jumlahBekerja' => $jumlahBekerja,
            'jumlahWirausaha' => $jumlahWirausaha,
            'jumlahStudiLanjut' => $jumlahStudiLanjut,
            'jenisInstansiStats' => $jenisInstansiStats,
            'averageGaji' => $averageGaji,
            'keselarasanPekerjaan' => $keselarasanPekerjaan,
            'keselarasanStudi' => $keselarasanStudi,
            'alumniTracerHistory' => $alumniTracerHistory,
            'alumniTracerTahunIniStatus' => $alumniTracerTahunIniStatus,
            'alumniTracerTahunIni' => $alumniTracerTahunIni,
            'alumniBisaIsiLagiPada' => $alumniBisaIsiLagiPada,
            'alumniCanFillTracer' => $alumniCanFillTracer,
        ]);
    }
}
