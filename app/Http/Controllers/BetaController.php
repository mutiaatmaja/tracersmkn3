<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;

class BetaController extends Controller
{
    public function betaCetakDompdf()
    {
        return Pdf::view('beta.cetak')
            ->format('a4')
            ->name('tracer-report.pdf');
    }

    public function betaCetakCloudflare()
    {
        return Pdf::view('beta.cetak')
            ->format('a4')
            ->name('tracer-report-cloudflare.pdf');
    }
}

