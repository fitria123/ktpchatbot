<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Laporan Dokumen</title>
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
			<div style="font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">DINAS KEPENDUDUKAN
				DAN <br> PENCATATAN SIPIL</div>
			<div style="font-size: 14px; line-height: 1.5;">Jalan P. Diponegoro Nomor 150 Telepon (0283)671322 Faksimile
				(0283)671322</div>
		</div>
		<div style="margin-top: 10px; border-bottom: 1px solid black;"></div>
		<div style="margin-top: 2px; border-bottom: 5px solid black;"></div>
	</div>

	<h2 style="text-align:center">Laporan Permohonan Cetak</h2>
	<p><strong>Bulan:</strong> {{ $bulan }} | <strong>Tahun:</strong> {{ $tahun }}</p>

	<table>
		<thead>
			<tr>
				<th width="3%">No.</th>
				<th width="18%">Nama</th>
				<th width="13%">NIK</th>
				<th width="13%">KK</th>
				<th width="13%">Desa/Kelurahan</th>
				<th width="13%">Alasan</th>
				<th width="10%">Status</th>
				<th width="10%">Tanggal</th>
			</tr>
		</thead>
		<tbody>
			@forelse($requests as $i => $r)
				<tr>
					<td class="center">{{ $i + 1 }}</td>
					<td>{{ $r->user->name ?? '-' }}</td>
					<td class="center small">{{ $r->nik }}</td>
					<td class="center small">{{ $r->kk }}</td>
					<td>{{ $r->desa_kelurahan }}</td>
					<td>{{ ucfirst($r->alasan) }}</td>
					<td class="center">{{ ucfirst($r->status) }}</td>
					<td class="center small">{{ \Carbon\Carbon::parse($r->created_at)->format('d-m-Y') }}</td>
				</tr>
			@empty
				<tr>
					<td colspan="8" style="text-align: center">Tidak ada data.</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</body>

</html>
