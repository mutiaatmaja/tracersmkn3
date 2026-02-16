<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Tracer Study</title>

    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 40px;
            color: #2d3748;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 3px 0;
            font-size: 12px;
            color: #4b5563;
        }

        .section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #1e40af;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table th {
            background: #1e40af;
            color: white;
            padding: 8px;
            font-size: 12px;
        }

        table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .status-ok {
            color: #16a34a;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>LAPORAN TRACER STUDY ALUMNI</h1>
        <p>SMKN 3 Pontianak</p>
        <p>Sistem Informasi Tracer Study</p>
    </div>

    <!-- Informasi Laporan -->
    <div class="section">
        <div class="section-title">&#128202; Informasi Laporan</div>

        <p><strong>Tanggal Cetak:</strong> {{ now()->format('d-m-Y H:i') }}</p>
        <p><strong>Tipe Laporan:</strong> Data Alumni</p>
        <p><strong>Status:</strong> <span class="status-ok">&#10004; Berhasil Dibuat</span></p>
    </div>

    <!-- Data Alumni -->
    <div class="section">
        <div class="section-title">&#128101; Data Alumni</div>

        <table>
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Tahun Lulus</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Siswa Test 1</td>
                    <td>Bekerja</td>
                    <td>2024</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Siswa Test 2</td>
                    <td>Melanjutkan Studi</td>
                    <td>2024</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Siswa Test 3</td>
                    <td>Belum Bekerja</td>
                    <td>2024</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Statistik -->
    <div class="section">
        <div class="section-title">&#128200; Statistik Alumni</div>

        <p>Total Alumni: <strong>3 orang</strong></p>
        <p>Yang Bekerja: <strong>1 orang (33%)</strong></p>
        <p>Melanjutkan Studi: <strong>1 orang (33%)</strong></p>
        <p>Belum Bekerja: <strong>1 orang (33%)</strong></p>
    </div>

    <div class="footer">
        © {{ date('Y') }} SMKN 3 Pontianak — Sistem Tracer Study Alumni
    </div>

</body>

</html>
