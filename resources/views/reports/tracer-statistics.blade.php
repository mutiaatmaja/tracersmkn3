<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Statistik Tracer Study</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            line-height: 1.4;
            margin: 18px;
        }

        h1 {
            margin: 0;
            font-size: 20px;
        }

        .subtitle {
            margin-top: 4px;
            color: #4b5563;
            font-size: 12px;
        }

        .summary {
            margin-top: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
        }

        .grid {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
        }

        .grid td {
            width: 25%;
            border: 1px solid #e5e7eb;
            padding: 8px;
            vertical-align: top;
        }

        .label {
            color: #6b7280;
            font-size: 11px;
        }

        .value {
            margin-top: 2px;
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }

        .section-title {
            margin-top: 18px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
        }

        table.data th {
            background: #f9fafb;
            font-weight: bold;
            color: #111827;
        }

        .text-right {
            text-align: right;
        }

        .two-col {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
            padding-right: 8px;
        }

        .muted {
            color: #6b7280;
        }
    </style>
</head>

<body>
    <h1>Laporan Statistik Tracer Study Alumni</h1>
    <p class="subtitle">SMKN 3 Pontianak · Dicetak {{ now()->format('d M Y H:i') }}</p>

    <div class="summary">
        <strong>Filter Periode:</strong>
        @if ($periodeFilter)
            {{ $periodeFilter }}
        @else
            Semua Periode
        @endif
    </div>

    <table class="grid">
        <tr>
            <td>
                <div class="label">Total Respon</div>
                <div class="value">{{ number_format($totalRespon) }}</div>
            </td>
            <td>
                <div class="label">Submitted</div>
                <div class="value">{{ number_format($submitted) }}</div>
            </td>
            <td>
                <div class="label">Draft</div>
                <div class="value">{{ number_format($draft) }}</div>
            </td>
            <td>
                <div class="label">Persentase Submitted</div>
                <div class="value">{{ $persenSubmitted }}%</div>
            </td>
        </tr>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <div class="section-title">Respon per Periode</div>
                <table class="data">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="text-right">Jumlah Respon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tracerPerPeriode as $row)
                            <tr>
                                <td>{{ $row->periode_tahun }}</td>
                                <td class="text-right">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="muted">Belum ada data tracer study.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td>
                <div class="section-title">Status Kegiatan Alumni (B1/B2)</div>
                <table class="data">
                    <tbody>
                        <tr>
                            <th>Melanjutkan Studi</th>
                            <td class="text-right">{{ number_format($statusKegiatan['studi_lanjut']) }}</td>
                        </tr>
                        <tr>
                            <th>Bekerja / Berwirausaha</th>
                            <td class="text-right">{{ number_format($statusKegiatan['bekerja']) }}</td>
                        </tr>
                        <tr>
                            <th>Belum Studi & Belum Bekerja</th>
                            <td class="text-right">{{ number_format($statusKegiatan['belum_keduanya']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Top Negara Tinggal Alumni</div>
    <table class="data">
        <thead>
            <tr>
                <th>Negara</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topNegara as $row)
                <tr>
                    <td>{{ $row->a2_negara_tinggal }}</td>
                    <td class="text-right">{{ number_format($row->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="muted">Belum ada data negara tracer.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Statistik per Pertanyaan Tracer (dengan Persentase)</div>
    @forelse ($questionStats as $question)
        <div style="margin-bottom: 12px;">
            <p style="margin: 0 0 4px; font-weight: bold;">{{ $question['label'] }}</p>
            <p class="muted" style="margin: 0 0 6px;">Respon terisi: {{ number_format($question['answered']) }}</p>

            <table class="data">
                <thead>
                    <tr>
                        <th>Opsi Jawaban</th>
                        <th class="text-right">Jumlah</th>
                        <th class="text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($question['options'] as $option)
                        <tr>
                            <td>{{ $option['label'] }}</td>
                            <td class="text-right">{{ number_format($option['count']) }}</td>
                            <td class="text-right">{{ $option['percent'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <p class="muted">Belum ada data tracer untuk statistik per pertanyaan.</p>
    @endforelse
</body>

</html>
