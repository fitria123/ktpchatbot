@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 shadow rounded-lg text-sm">
        <h2 class="text-lg font-semibold mb-4">Detail Permohonan Cetak</h2>

        {{-- Alert sukses/error --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabel Informasi User --}}
        <table class="w-full mb-5">
            <tbody>
                <tr>
                    <td class="py-1 font-medium w-40">Nama</td>
                    <td class="py-1">{{ $request->user->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">NIK</td>
                    <td class="py-1">{{ $request->nik }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">No KK</td>
                    <td class="py-1">{{ $request->kk ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">No Antrian</td>
                    <td class="py-1">{{ $request->antrian->nomor ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Desa/Kelurahan</td>
                    <td class="py-1">{{ $request->desa_kelurahan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Alasan Pencetakan</td>
                    <td class="py-1">{{ $request->alasan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Pengambil</td>
                    <td class="py-1">{{ $request->take?->nama_pengambil ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Nik Pengambil</td>
                    <td class="py-1">{{ $request->take?->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Status</td>
                    <td class="py-1">
                        <span
                            class="px-2 py-0.5 rounded text-[11px]
                        @switch($request->status)
                            @case('dalam proses pencetakan') bg-blue-100 text-blue-700 @break
                            @case('sudah tercetak') bg-indigo-100 text-indigo-700 @break
                            @case('selesai pengambilan') bg-green-100 text-green-700 @break
                            @case('ditolak') bg-red-100 text-red-700 @break
                        @endswitch">
                            {{ ucfirst($request->status) }}
                        </span>
                        @if ($request->status == 'ditolak' && $request->alasan_ditolak)
                            <div class="mt-1 text-red-600 text-xs">Alasan: {{ $request->alasan_ditolak }}</div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Tabel File Upload --}}
        <table class="w-full mb-6 text-xs">
            @php
                $uploads = [
                    'Kartu Keluarga (KK)' => 'file_kk',
                    'KTP Lama' => 'file_ktp_lama',
                    'Surat Kehilangan' => 'file_surat_kehilangan',
                    'Swafoto' => 'file_swafoto',
                ];
            @endphp
            @foreach ($uploads as $label => $field)
                <tr>
                    <td class="py-1 font-medium w-40">{{ $label }}</td>
                    <td class="py-1">
                        @if (optional($request->upload)->$field)
                            <a href="{{ asset('storage/' . optional($request->upload)->$field) }}" target="_blank"
                                class="text-blue-600 underline">Lihat</a>
                        @else
                            <span class="text-gray-500 italic">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        {{-- Form Ubah Status --}}
        <form action="{{ route('admin.document.updateStatus', $request->id) }}" method="POST" class="space-y-3">
            @csrf
            @php
                $isLocked = in_array($request->status, ['ditolak', 'selesai pengambilan']);
                $statusSteps = ['dalam proses pencetakan', 'sudah tercetak', 'selesai pengambilan', 'ditolak'];
                $currentIndex = array_search($request->status, $statusSteps);
            @endphp

            <div>
                <label class="block text-xs font-medium mb-1">Ubah Status</label>
                <select id="status-select" name="status" class="w-full border rounded px-2 py-1 text-sm"
                    {{ $isLocked ? 'disabled' : '' }}>
                    @foreach ($statusSteps as $index => $status)
                        <option value="{{ $status }}" {{ $request->status == $status ? 'selected' : '' }}
                            {{ $index < $currentIndex ? 'disabled' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="alasan-penolakan-container" class="transition-all mt-2"
                style="display: {{ $request->status == 'ditolak' ? 'block' : 'none' }};">
                <label class="block text-xs font-medium mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                <select name="alasan_ditolak" id="alasan_ditolak" class="w-full border rounded px-2 py-1 text-sm"
                    {{ $isLocked ? 'disabled' : '' }}>
                    <option value="">-- Pilih Alasan --</option>
                    @foreach (['informasi tidak sesuai', 'kualitas gambar terlalu rendah', 'dokumen tidak terbaca jelas', 'dokumen tidak valid', 'persyaratan belum lengkap', 'tidak memenuhi syarat'] as $alasan)
                        <option value="{{ $alasan }}" {{ $request->alasan_ditolak == $alasan ? 'selected' : '' }}>
                            {{ ucfirst($alasan) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const statusSelect = document.getElementById('status-select');
                    const alasanContainer = document.getElementById('alasan-penolakan-container');
                    const alasanSelect = document.getElementById('alasan_ditolak');

                    if (!statusSelect || !alasanContainer || !alasanSelect) return;

                    function toggleAlasan() {
                        if (statusSelect.value === 'ditolak') {
                            alasanContainer.style.display = 'block';
                            alasanSelect.setAttribute('required', 'required');
                        } else {
                            alasanContainer.style.display = 'none';
                            alasanSelect.removeAttribute('required');
                            alasanSelect.value = '';
                        }
                    }

                    toggleAlasan();
                    statusSelect.addEventListener('change', toggleAlasan);
                });
            </script> --}}



            @unless ($isLocked)
                <button type="submit"
                    class="mt-2 px-3 py-1.5 rounded bg-blue-600 text-white text-sm hover:bg-blue-700 transition">Simpan</button>
            @endunless
        </form>
    </div>
@endsection
