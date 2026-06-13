@extends('layouts.admin')

@section('title', 'Ambil Antrian untuk User')

@section('content')
    <div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
            Manajemen Antrian User - Hari Ini ({{ $today->format('d M Y') }})
        </h2>

        {{-- Notifikasi --}}
        @if (session('info'))
            <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-4">{{ session('info') }}</div>
        @endif

        {{-- Form pencarian --}}
        <div class="mb-4">
            <form action="{{ route('admin.antrian.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Cari nama warga..."
                    class="border border-gray-300 rounded px-4 py-2 w-full md:w-64 focus:ring focus:outline-none" />
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Cari
                </button>
                @if (request()->has('keyword') && request()->get('keyword') !== '')
                    <a href="{{ route('admin.antrian.index') }}"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 text-sm">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Stok habis --}}
        @if ($stokBlangko <= 0)
            <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Stok blangko habis.</span>
            </div>
        @endif

        {{-- Tabel daftar user --}}
        <table class="w-full text-sm text-left">
            <thead>
                <tr>
                    <th class="pb-2 border-b">Nama</th>
                    <th class="pb-2 border-b">NIK</th>
                    <th class="pb-2 border-b">Status Dokumen</th>
                    <th class="pb-2 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php
                        $antrian = $antriansToday[$user->id] ?? null;
                        $docStatus = $userDocStatuses[$user->id] ?? 'Belum ada pengajuan';

                        // Jika sudah ambil antrian tapi belum ada DocumentRequest → status jadi "dalam pengajuan"
                        if ($antrian && $docStatus === 'Belum ada pengajuan') {
                            $docStatus = 'dalam pengajuan';
                        }

                        // Cek apakah ada document request untuk antrian user ini
                        $hasDocRequest = false;
                        if ($antrian) {
                            $hasDocRequest = \App\Models\DocumentRequest::where('antrian_id', $antrian->id)
                                ->where('user_id', $user->id)
                                ->exists();
                        }
                    @endphp

                    <tr class="border-b">
                        <td class="py-2">{{ $user->name }}</td>
                        <td>{{ $user->nik }}</td>
                        <td>
                            <span class="font-semibold text-gray-800">{{ ucfirst($docStatus) }}</span>
                        </td>
                        <td>
                            @php
                                $cannotTake =
                                    $user->status === 'inactive' ||
                                    ($antrian && $hasDocRequest && $docStatus !== 'ditolak');

                                // Cek jika status dokumen ditolak
                                $isRejected = $docStatus === 'ditolak';
                            @endphp

                            @if ($isRejected)
                                <span class="text-gray-400 text-sm">Tidak bisa ambil</span>
                                {{-- <button disabled class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                                    Edit Pengajuan
                                </button> --}}
                            @elseif ($cannotTake)
                                <span class="text-gray-400 text-sm">Tidak bisa ambil</span>
                            @else
                                <form action="{{ route('admin.antrian.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm
                {{ $stokBlangko <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $stokBlangko <= 0 ? 'disabled' : '' }}>
                                        Ambil Antrian
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
