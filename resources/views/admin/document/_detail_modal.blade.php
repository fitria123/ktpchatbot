    <div class="max-w-2xl mx-auto text-sm">
        <h2 class="text-lg font-semibold mb-3">Detail Permohonan Cetak</h2>

        {{-- Tabel Informasi User --}}
        <table class="w-full mb-4">
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
                    <td class="py-1 font-medium">NIK Pengambil</td>
                    <td class="py-1">{{ $request->take?->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-medium">Status Permohonan</td>
                    <td class="py-1">
                        <span
                            class="px-2 py-0.5 rounded text-[11px]
                        @switch($request->status)
                            {{-- @case('dalam proses verifikasi') bg-yellow-100 text-yellow-700 @break --}}
                            @case('dalam proses pencetakan') bg-blue-100 text-blue-700 @break
                            @case('sudah tercetak') bg-indigo-100 text-indigo-700 @break
                            @case('selesai pengambilan') bg-green-100 text-green-700 @break
                            @case('ditolak') bg-red-100 text-red-700 @break
                        @endswitch">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- File Upload --}}
        <table class="w-full mb-5 text-xs">
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
        <form action="{{ route('admin.document.updateStatus', $request->id) }}" method="POST"
            class="space-y-3 text-sm">
            @csrf

            @php
                $isLocked = in_array($request->status, ['ditolak', 'selesai pengambilan']);
                $statusSteps = [
                    // 'dalam proses verifikasi',
                    'dalam proses pencetakan',
                    'sudah tercetak',
                    'selesai pengambilan',
                    'ditolak',
                ];
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

            {{-- Alasan Penolakan --}}
            <div class="transition-all mt-2" data-alasan-container>
                <label class="block text-xs font-medium mb-1">Alasan Penolakan <span
                        class="text-red-500">*</span></label>
                <input type="text" name="alasan_ditolak" data-alasan-input
                    class="w-full border rounded px-2 py-1 text-sm" value="{{ $request->alasan_ditolak ?? '' }}"
                    placeholder="Masukkan alasan penolakan">
            </div>

            @unless ($isLocked)
                <button type="submit"
                    class="mt-2 px-3 py-1.5 rounded bg-blue-600 text-white text-sm hover:bg-blue-700 transition">
                    Simpan
                </button>
            @endunless
        </form>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form').forEach(form => {
                    const statusSelect = form.querySelector('[data-status-select]');
                    const alasanContainer = form.querySelector('[data-alasan-container]');
                    const alasanInput = form.querySelector('[data-alasan-input]');

                    if (!statusSelect || !alasanContainer || !alasanInput) return;

                    // Fungsi toggle
                    const toggleAlasan = () => {
                        if (statusSelect.value.toLowerCase() === 'ditolak') {
                            alasanInput.disabled = false;
                            alasanInput.required = true;
                        } else {
                            alasanInput.disabled = true;
                            alasanInput.required = false;
                            alasanInput.value = '';
                        }
                    };

                    // Cek awal
                    toggleAlasan();

                    // Event saat dropdown berubah
                    statusSelect.addEventListener('change', toggleAlasan);
                });
            });
        </script>
    @endpush
