@extends('layouts.admin')

@section('title', 'Detail Stok Blangko')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-2xl font-bold mb-4 text-blue-700">Detail Stok Blangko</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm mb-6">
                <tbody>
                    <tr>
                        <td class="px-4 py-2 font-semibold w-1/4 bg-gray-50">No Bast</td>
                        <td class="px-4 py-2">{{ $blangko->no_bast }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-semibold bg-gray-50">Jumlah</td>
                        <td class="px-4 py-2">{{ $blangko->jumlah_blanko }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-semibold bg-gray-50">Tanggal</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($blangko->tanggal)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-semibold bg-gray-50">Waktu</td>
                        <td class="px-4 py-2">{{ $blangko->waktu }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between">
            <a href="{{ route('admin.blangko.index') }}" class="text-blue-600 hover:underline">Kembali</a>
            {{-- <a href="{{ route('admin.blangko.print', $blangko->id) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold" target="_blank">Print</a> --}}
        </div>
    </div>
@endsection
