@extends('layouts.admin')

@section('title', 'Tambah Permohonan Cetak')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-4 text-blue-700">Tambah Permohonan Cetak</h2>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.document.store') }}" method="POST">
            @csrf

            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="hidden" name="antrian_id" value="{{ $antrian->id }}">

            <div class="mb-3">
                <label class="block mb-1 font-semibold">Nama Pemohon</label>
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ old('nama', $user->name) }}" readonly>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-semibold">Nomor KK</label>
                <input maxlength="16" type="text" name="kk" class="w-full border rounded px-3 py-2"
                    value="{{ old('kk') }}" placeholder="Masukkan Nomor KK" required>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-semibold">NIK</label>
                <input type="text" name="nik" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ $user->nik }}" readonly>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-semibold">Desa / Kelurahan</label>
                <select name="desa_kelurahan" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Desa/Kelurahan --</option>
                    @foreach ($desaList as $desa)
                        <option value="{{ $desa }}" {{ old('desa_kelurahan') == $desa ? 'selected' : '' }}>
                            {{ $desa }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block mb-1 font-semibold">Alasan Pencetakan</label>
                <select name="alasan" id="alasanSelect" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Alasan --</option>
                    <option value="Pembuatan KTP Baru" {{ old('alasan') == 'baru' ? 'selected' : '' }}>
                        Membuat KTP (Baru)
                    </option>
                    <option value="Pembuatan KTP Rusak" {{ old('alasan') == 'rusak' ? 'selected' : '' }}>
                        Membuat KTP (Rusak)
                    </option>
                    <option value="pembaruan data KTP" {{ old('alasan') == 'pembaruan data' ? 'selected' : '' }}>
                        Membuat KTP (Pembaruan Data)
                    </option>
                    <option value="KTP hilang" {{ old('alasan') == 'hilang' ? 'selected' : '' }}>
                        Membuat KTP (Hilang)
                    </option>
                </select>
            </div>

            <div class="flex justify-between mt-4">
                <a href="{{ route('admin.document.index') }}" class="text-blue-600 hover:underline">Kembali</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
