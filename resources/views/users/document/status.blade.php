@extends('layouts.user')

@section('title', 'Status Permohonan Cetak')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-bold mb-4 text-gray-800">Status Permohonan Cetak Anda</h2>

        {{-- Notifikasi --}}
        @if (session('info'))
            <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4">
                {{ session('info') }}

                @if (session('antrian_id'))
                    <a href="{{ route('user.document.create') }}" class="underline font-bold ml-2">
                        Lanjutkan Pengajuan
                    </a>
                @endif
            </div>
        @endif

        {{-- Tabel Data --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-700">
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Nomor Antrian</th>
                        <th class="px-4 py-2 text-left">Desa/Kelurahan</th>
                        <th class="px-4 py-2 text-left">Nomor KK</th>
                        <th class="px-4 py-2 text-left">NIK</th>
                        <th class="px-4 py-2 text-left">Alasan Percetakan</th>
                        @if ($requests->contains('status', 'ditolak'))
                            <th class="px-4 py-2 text-left">Alasan Ditolak</th>
                        @endif
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                {{ \Carbon\Carbon::parse($request->created_at)->format('d-m-Y H:i') }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $request->antrian ? $request->antrian->nomor : '-' }}
                            </td>
                            <td class="px-4 py-2">{{ $request->desa_kelurahan }}</td>
                            <td class="px-4 py-2">{{ $request->kk }}</td>
                            <td class="px-4 py-2">{{ $request->nik }}</td>
                            <td class="px-4 py-2">{{ $request->alasan }}</td>

                            @if ($requests->contains('status', 'ditolak'))
                                <td class="px-4 py-2 text-red-600 text-sm">
                                    {{ $request->status == 'ditolak' ? $request->alasan_ditolak ?? '-' : '-' }}
                                </td>
                            @endif

                            <td class="px-4 py-2">
                                <span
                                    class="px-2 py-1 rounded text-xs
                                    @if ($request->status == 'dalam proses verifikasi') bg-yellow-100 text-yellow-700
                                    @elseif($request->status == 'dalam proses pencetakan') bg-blue-100 text-blue-700
                                    @elseif($request->status == 'sudah tercetak') bg-indigo-100 text-indigo-700
                                    @elseif($request->status == 'selesai pengambilan') bg-green-100 text-green-700
                                    @elseif($request->status == 'ditolak') bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                                Belum ada permohonan cetak.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tombol kembali --}}
        <div class="mt-6">
            <a href="{{ route('user.index') }}"
                class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded font-semibold">
                ← Kembali
            </a>
        </div>
    </div>
@endsection
