@extends('layouts.admin')

@section('title', 'Pengambilan e-KTP')

@section('content')
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Pengambilan e-KTP</h1>
        <p class="text-gray-600">Daftar e-KTP yang sudah tercetak dan pengambilan dokumen.</p>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar e-KTP Sudah Tercetak</h2>
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">No.</th>
                        <th class="px-4 py-2 text-left">Nama Pemohon</th>
                        <th class="px-4 py-2 text-left">NIK</th>
                        <th class="px-4 py-2 text-left">Desa/Kelurahan</th>
                        <th class="px-4 py-2 text-left">Nama Pengambil</th>
                        <th class="px-4 py-2 text-left">Tanggal Pengambilan</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($ektp->count() > 0)
                        @foreach ($ektp as $i => $request)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $i + 1 }}</td>
                                <td class="px-4 py-2">{{ $request->user->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $request->nik }}</td>
                                <td class="px-4 py-2">{{ $request->desa_kelurahan }}</td>
                                <td class="px-4 py-2">
                                    {{ optional($request->takeEktp)->nama_pengambil ?? 'Belum diambil' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ optional($request->takeEktp)->tanggal_pengambilan
                                        ? \Carbon\Carbon::parse($request->takeEktp->tanggal_pengambilan)->format('d-m-Y')
                                        : 'Belum diambil' }}
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.document.takeEktpShow', $request->id) }}"
                                        class="inline-block px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs">
                                        Input Pengambilan
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                                Belum ada e-KTP yang tercetak.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


    {{-- 🔎 Form Pencarian khusus tabel bawah --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-4 bg-gray-50 p-4 rounded-lg shadow-sm">
        <div>
            <label class="block text-xs font-semibold mb-1">NIK Pemohon / Pengambil</label>
            <input type="text" name="nik" value="{{ request('nik') }}"
                class="border rounded px-3 py-2 text-sm focus:ring focus:ring-blue-300"
                placeholder="Cari NIK Pemohon atau Pengambil">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                Cari
            </button>
            <a href="{{ route('admin.document.takeEktp') }}"
                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-300 transition">
                Reset
            </a>
        </div>
    </form>

    {{-- 📋 Daftar Pengambilan (selalu tampil) --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Daftar Pengambilan</h2>
        <p class="text-gray-600 mb-4 text-sm">Berikut adalah daftar pengambilan dokumen beserta NIK pemohon dan pengambil.
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">No.</th>
                        <th class="px-6 py-3 text-left">NIK Pemohon</th>
                        <th class="px-6 py-3 text-left">Nama Pemohon</th>
                        <th class="px-6 py-3 text-left">Nama Pengambil</th>
                        <th class="px-6 py-3 text-left">NIK Pengambil</th>
                        <th class="px-6 py-3 text-left">Tanggal Pengambilan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pengambilan as $i => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-gray-700">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $item->documentRequest->nik ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $item->documentRequest->user->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $item->nama_pengambil }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $item->nik }}</td>
                            <td class="px-6 py-3 text-gray-700">
                                {{ $item->tanggal_pengambilan ? \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d-m-Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                Belum ada data pengambilan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
