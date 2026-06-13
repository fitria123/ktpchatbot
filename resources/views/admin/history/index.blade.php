@extends('layouts.admin')
@section('title', 'Riwayat Pendaftaran User')
@section('content')
	<div class="bg-white rounded-xl shadow p-6">
		<h2 class="text-xl font-bold mb-4 text-gray-800">Riwayat Pendaftaran User</h2>
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200 text-sm">
				<thead>
					<tr class="bg-gray-50 text-gray-700">
						<th class="px-4 py-2 text-left">No.</th>
						<th class="px-4 py-2 text-left">Nama</th>
						<th class="px-4 py-2 text-left">NIK</th>
						<th class="px-4 py-2 text-left">Desa/Kelurahan</th>
						<th class="px-4 py-2 text-left">Alasan</th>
						<th class="px-4 py-2 text-left">Status</th>
						<th class="px-4 py-2 text-left">Tanggal Daftar</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($histories as $i => $history)
						<tr>
							<td class="px-4 py-2">{{ $i + 1 }}</td>
							<td class="px-4 py-2">{{ $history->nama }}</td>
							<td class="px-4 py-2">{{ $history->nik }}</td>
							<td class="px-4 py-2">{{ $history->desa_kelurahan }}</td>
							<td class="px-4 py-2">{{ $history->alasan }}</td>
							<td class="px-4 py-2">
								<span
									class="px-2 py-1 rounded text-xs
                                    @if ($history->status == 'menunggu') bg-yellow-100 text-yellow-700
                                    @elseif($history->status == 'diproses') bg-blue-100 text-blue-700
                                    @elseif($history->status == 'selesai') bg-green-100 text-green-700 @endif">
									{{ ucfirst($history->status) }}
								</span>
							</td>
							<td class="px-4 py-2">
								{{ \Carbon\Carbon::parse($history->tanggal_pendaftaran)->format('d-m-Y H:i') }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@endsection
