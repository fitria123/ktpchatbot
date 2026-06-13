@extends('layouts.user')

@section('title', 'Edit Permintaan Cetak KTP-el')

@section('content')
    <div class="bg-white rounded-xl shadow p-6 max-w-2xl mx-auto mt-12">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Edit Permohonan Cetak KTP-el</h2>

        {{-- Notifikasi error --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 rounded">
                <strong class="block text-red-800 mb-2">Terjadi kesalahan!</strong>
                <ul class="list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 rounded">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Notifikasi error stok --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 rounded">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('user.document.update', $document->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Nomor KK --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">Nomor KK</label>
                <input type="text" name="kk" class="w-full border rounded px-3 py-2"
                    value="{{ old('kk', $document->kk) }}">
            </div>

            {{-- NIK --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">NIK</label>
                <input type="text" name="nik" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ $document->nik }}" readonly>
            </div>

            {{-- Desa/Kelurahan --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">Desa/Kelurahan</label>
                <select name="desa_kelurahan" class="w-full border rounded px-3 py-2" required>
                    @foreach ($desaList as $desa)
                        <option value="{{ $desa }}" {{ $document->desa_kelurahan == $desa ? 'selected' : '' }}>
                            {{ $desa }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Alasan Permohonan --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">Alasan Permohonan</label>
                <select name="alasan" id="alasanSelect" class="w-full border rounded px-3 py-2" required>
                    <option value="Pembuatan KTP Baru" {{ $document->alasan == 'Pembuatan KTP Baru' ? 'selected' : '' }}>
                        Membuat KTP (Baru)
                    </option>
                    <option value="Pembuatan KTP Rusak" {{ $document->alasan == 'Pembuatan KTP Rusak' ? 'selected' : '' }}>
                        Membuat KTP (Rusak)
                    </option>
                    <option value="pembaruan data KTP" {{ $document->alasan == 'pembaruan data KTP' ? 'selected' : '' }}>
                        Membuat KTP (Pembaruan Data)
                    </option>
                    <option value="KTP hilang" {{ $document->alasan == 'KTP hilang' ? 'selected' : '' }}>
                        Membuat KTP (Hilang)
                    </option>
                </select>
            </div>

            {{-- Upload KK --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">KK</label>
                @if ($document->upload && $document->upload->file_kk)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $document->upload->file_kk) }}" alt="KK" class="w-32 h-auto border rounded">
                        <p class="text-xs text-gray-500 mt-1">File saat ini (upload baru jika ingin mengganti)</p>
                    </div>
                @endif
                <input type="file" name="file_kk" class="w-full border rounded px-3 py-2" accept="image/*">
            </div>

            {{-- KTP Lama --}}
            <div id="ktpLamaField" class="mb-3">
                <label class="block mb-1 font-semibold">KTP Lama</label>
                @if ($document->upload && $document->upload->file_ktp_lama)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $document->upload->file_ktp_lama) }}" alt="KTP Lama" class="w-32 h-auto border rounded">
                        <p class="text-xs text-gray-500 mt-1">File saat ini (upload baru jika ingin mengganti)</p>
                    </div>
                @endif
                <input type="file" name="file_ktp_lama" class="w-full border rounded px-3 py-2" accept="image/*">
                <p id="ktpLamaNotif" class="text-red-500 text-sm mt-1 hidden">File KTP Lama wajib di-upload untuk alasan ini.</p>
            </div>

            {{-- Surat Kehilangan --}}
            <div id="suratHilangField" class="mb-3">
                <label class="block mb-1 font-semibold">Surat Kehilangan</label>
                @if ($document->upload && $document->upload->file_surat_kehilangan)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $document->upload->file_surat_kehilangan) }}" alt="Surat Kehilangan"
                            class="w-32 h-auto border rounded">
                        <p class="text-xs text-gray-500 mt-1">File saat ini (upload baru jika ingin mengganti)</p>
                    </div>
                @endif
                <input type="file" name="file_surat_kehilangan" class="w-full border rounded px-3 py-2" accept="image/*">
                <p id="suratHilangNotif" class="text-red-500 text-sm mt-1 hidden">File Surat Kehilangan wajib di-upload untuk alasan ini.</p>
            </div>

            {{-- Swafoto --}}
            <div class="mb-3">
                <label class="block mb-1 font-semibold">Swafoto</label>
                @if ($document->upload && $document->upload->file_swafoto)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $document->upload->file_swafoto) }}" alt="Swafoto"
                            class="w-32 h-auto border rounded">
                        <p class="text-xs text-gray-500 mt-1">File saat ini (upload baru jika ingin mengganti)</p>
                    </div>
                @endif
                <input type="file" name="file_swafoto" class="w-full border rounded px-3 py-2" accept="image/*">
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold mt-2">
                Update Permohonan
            </button>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alasanSelect = document.getElementById('alasanSelect');
                const ktpLamaField = document.getElementById('ktpLamaField');
                const suratHilangField = document.getElementById('suratHilangField');
                const ktpLamaNotif = document.getElementById('ktpLamaNotif');
                const suratHilangNotif = document.getElementById('suratHilangNotif');

                function toggleFields() {
                    const alasan = alasanSelect.value;

                    // KTP Lama - untuk alasan Rusak atau Pembaruan Data
                    if (alasan === 'Pembuatan KTP Rusak' || alasan === 'pembaruan data KTP') {
                        ktpLamaField.style.display = 'block';
                        ktpLamaNotif.style.display = 'none';
                        // JANGAN PAKAI required - biarkan user upload jika ingin mengganti saja
                        // ktpLamaField.querySelector('input').required = false; // default sudah false
                    } else {
                        ktpLamaField.style.display = 'none';
                        ktpLamaNotif.style.display = 'none';
                        // ktpLamaField.querySelector('input').required = false;
                    }

                    // Surat Kehilangan - untuk alasan Hilang
                    if (alasan === 'KTP hilang') {
                        suratHilangField.style.display = 'block';
                        suratHilangNotif.style.display = 'none';
                        // JANGAN PAKAI required
                        // suratHilangField.querySelector('input').required = false;
                    } else {
                        suratHilangField.style.display = 'none';
                        suratHilangNotif.style.display = 'none';
                        // suratHilangField.querySelector('input').required = false;
                    }
                }

                // Jalankan saat dropdown berubah
                alasanSelect.addEventListener('change', toggleFields);

                // Inisialisasi saat halaman dimuat
                toggleFields();
            });
        </script>
    @endpush
@endsection