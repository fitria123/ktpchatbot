<!DOCTYPE html>
<html>

<head>
    <title>Laporan Blangko</title>

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            position: relative;
        }

        .header img.logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 90px;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 11px;
            word-break: break-word;
        }

        th {
            background-color: #eee;
            text-align: center;
        }

        td.center {
            text-align: center;
        }

        td.small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header" style="text-align: center; margin-bottom: 10px;">
        <img src="{{ public_path('images/logo_header.png') }}" alt="Logo Brebes" class="logo"
            style="width: 80px; position: absolute; top: 5px; left: 30px;">
        <div style="margin-left: 90px;">
            <div style="font-size: 14px; line-height: 1.5;">PEMERINTAH KABUPATEN BREBES</div>
            <div style="font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">DINAS
                KEPENDUDUKAN
                DAN <br> PENCATATAN SIPIL</div>
            <div style="font-size: 14px; line-height: 1.5;">Jalan P. Diponegoro Nomor 150 Telepon (0283)671322 Faksimile
                (0283)671322</div>
        </div>
        <div style="margin-top: 10px; border-bottom: 1px solid black;"></div>
        <div style="margin-top: 2px; border-bottom: 5px solid black;"></div>
    </div>

    <h2 style="text-align:center">Laporan Stock Blangko</h2>
    <p><strong>Bulan: {{ $bulan ?? 'Semua' }} | Tahun: {{ $tahun ?? 'Semua' }}</p>
 

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No BAST</th>
                <th>Jumlah Blangko</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalBlangko = 0;
            @endphp
            @foreach ($blangkos as $i => $blangko)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $blangko->tanggal }}</td>
                    <td>{{ $blangko->no_bast }}</td>
                    <td>{{ $blangko->jumlah_blanko }}</td>
                </tr>
                @php
                    $totalBlangko += $blangko->jumlah_blanko;
                @endphp
            @endforeach
            <tr>
                <td colspan="3"><strong>Jumlah Total</strong></td>
                <td><strong>{{ $totalBlangko }}</strong></td>
            </tr>
        </tbody>

    </table>
</body>

</html>
