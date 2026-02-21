<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Statistik Alumni</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 32px;
            color: #1f2937;
            font-size: 12px;
        }

        .header {
            border-bottom: 2px solid #1d4ed8;
            margin-bottom: 20px;
            padding-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 4px 0 0;
            color: #4b5563;
        }

        .summary {
            margin-bottom: 18px;
        }

        .summary table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        .summary .label {
            width: 45%;
            font-weight: 700;
            background: #f9fafb;
        }

        .section-title {
            margin: 14px 0 8px;
            font-weight: 700;
            color: #1d4ed8;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        .data-table th {
            background: #eff6ff;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            color: #6b7280;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Statistik Alumni</h1>
        <p>SMKN 3 Pontianak · Dicetak {{ now()->format('d-m-Y H:i') }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Filter Tahun Lulus</td>
                <td>{{ $tahunLulusFilter ?? 'Semua Tahun' }}</td>
            </tr>
            <tr>
                <td class="label">Total Alumni</td>
                <td>{{ number_format($totalAlumni) }}</td>
            </tr>
            <tr>
                <td class="label">Sudah Klaim Akun</td>
                <td>{{ number_format($sudahKlaim) }}</td>
            </tr>
            <tr>
                <td class="label">Belum Klaim Akun</td>
                <td>{{ number_format($belumKlaim) }}</td>
            </tr>
        </table>
    </div>

    <p class="section-title">Jumlah Alumni per Tahun Lulus</p>

    <table style="width:100%; border-collapse: collapse; margin-bottom: 8px;">
        <tr>
            <td style="width:50%; vertical-align: top; padding-right: 6px;">
                <p class="section-title" style="margin-top: 0;">Jumlah Alumni per Tahun Lulus</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tahun Lulus</th>
                            <th class="text-right">Jumlah Alumni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alumniPerTahun as $row)
                            <tr>
                                <td>{{ $row->tahun_lulus }}</td>
                                <td class="text-right">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Belum ada data alumni.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td style="width:50%; vertical-align: top; padding-left: 6px;">
                <p class="section-title" style="margin-top: 0;">Statistik Jenis Kelamin</p>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <th class="text-right">Jumlah Alumni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($statistikJenisKelamin as $row)
                            <tr>
                                <td>{{ str($row->jenis_kelamin)->title() }}</td>
                                <td class="text-right">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2">Belum ada data jenis kelamin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <p class="section-title">Statistik Umur</p>

    <table class="data-table">
        <tbody>
            <tr>
                <td>Rata-rata Usia</td>
                <td class="text-right">{{ $statistikUmur['rata_rata'] }} tahun</td>
            </tr>
            <tr>
                <td>Data Usia Terisi</td>
                <td class="text-right">{{ number_format($statistikUmur['total_terdata']) }}</td>
            </tr>
            <tr>
                <td>Data Usia Tidak Terisi</td>
                <td class="text-right">{{ number_format($statistikUmur['total_tidak_terdata']) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="data-table" style="margin-top: 8px;">
        <thead>
            <tr>
                <th>Kelompok Umur</th>
                <th class="text-right">Jumlah Alumni</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statistikUmur['bucket'] as $label => $jumlah)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">{{ number_format($jumlah) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} SMKN 3 Pontianak - Sistem Tracer Study Alumni
    </div>
</body>

</html>
