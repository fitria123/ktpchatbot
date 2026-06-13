@extends('layouts.admin')

@section('title', 'Stok Blangko KTP Elektronik')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-semibold mb-6">Stok Blangko KTP Elektronik</h1>

        <div class="flex justify-between items-center mb-4">
            <p class="text-lg">Jumlah Stok Saat Ini:</p>
            <span class="text-3xl font-bold text-green-600">
                {{ $stokSaatIni }}
            </span>
        </div>

        <a href="{{ route('admin.blangko.create') }}"
            class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-6">
            + Tambah Stok
        </a>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.blangko.print') }}" target="_blank"
            class="flex bg-gray-100 p-2 rounded shadow text-sm gap-2 max-w-[10px]">

            <div>
                <label class="block text-sm font-medium">Bulan</label>
                <select name="bulan" class="border px-2 py-1 rounded">
                    <option value="">Semua Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <!-- Tahun -->
            <div>
                <label class="block text-sm font-medium">Tahun</label>
                <select name="tahun" class="border px-2 py-1 rounded">
                    <option value="">Semua Tahun</option>
                    @for ($y = now()->year; $y >= 2022; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Tombol -->
            <div class="flex items-end">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Cetak Semua</button>
            </div>
        </form>

        <h2 class="text-xl font-semibold mt-8 mb-4">Riwayat Update Stok</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-700">
                        <th class="px-4 py-2 text-left">No.</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Stok Sebelumnya</th>
                        <th class="px-4 py-2 text-left">Jumlah yang ditambah</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Urutkan dari paling lama ke paling baru agar stok sebelumnya bisa dihitung benar
                        $riwayat = $riwayat->sortBy('created_at')->values();
                        $stokSebelumnya = 0;
                    @endphp
                    @forelse($riwayat as $i => $log)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $i + 1 }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2">{{ $stokSebelumnya }}</td>

                            <td class="px-4 py-2 font-bold">{{ $log->jumlah_blanko }}</td>
                            <td class="px-4 py-2 flex gap-2">
                                <a href="{{ route('admin.blangko.detail', $log->id) }}"
                                    class="inline-block px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                                    Detail
                                </a>
                                <form action="{{ route('admin.blangko.destroy', $log->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-block px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                                        Hapus
                                    </button>
                                </form>

                            </td>

                        </tr>
                        @php
                            $stokSebelumnya = $log->jumlah_total;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat update stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
