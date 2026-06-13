@extends('layouts.user')

@section('title', 'Ajukan Permohonan Cetak KTP-el')

@section('content')
    <div class="bg-white rounded-xl shadow p-6 max-w-2xl mx-auto">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-800 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-xl font-bold mb-4 text-gray-800">Ajukan Permohonan Cetak KTP-el</h2>

        @if (isset($stokBlangko) && $stokBlangko <= 0)
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 rounded text-red-800 font-semibold">
                Stok blangko KTP-el saat ini <b>habis</b>. Permintaan tidak dapat diajukan.
            </div>
        @endif

        <div class="mb-6 p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded">
            <strong class="block text-yellow-800 mb-2">Perhatian!</strong>
            <p class="text-yellow-700 mb-2">
                Sebelum melakukan pendaftaran cetak KTP-el, harap mempersiapkan beberapa hal berikut:
            </p>
            <ol class="list-decimal list-inside text-yellow-700 space-y-1">
                <li>Scan/foto Kartu Keluarga (KK) dalam format JPG/JPEG atau PNG dengan ukuran file max 2MB.</li>
                <li>Scan/foto KTP lama (jika ada) dalam format JPG/JPEG atau PNG dengan ukuran file max 2MB.</li>
                <li>Swafoto (selfie) terbaru dengan memegang KK dan KTP lama (jika ada).</li>
                <li>Jika permohonan cetak KTP karena kehilangan, harap upload surat kehilangan dari polsek setempat.</li>
            </ol>
        </div>

        <form action="{{ route('user.document.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
            @if (isset($stokBlangko) && $stokBlangko <= 0) style="pointer-events:none;opacity:0.6;" @endif>
            @csrf
            <div>
                <label class="block mb-1 font-semibold">Nomor KK</label>
                <input maxlength="16" type="text" name="kk" id="kkInput" class="w-full border rounded px-3 py-2"
                    value="{{ old('kk') ?? '' }}" placeholder="Masukan No.KK Anda" required
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif autocomplete="off">
                <div id="kkAlert" class="hidden mt-2 px-3 py-2 bg-red-100 text-red-700 text-sm rounded"></div>
            </div>
            <div>
                <label class="block mb-1 font-semibold">NIK</label>
                <input type="text" name="nik" id="nikInput" class="w-full border rounded px-3 py-2 bg-gray-100"
                    value="{{ Auth::user()->nik }}" readonly>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Desa/Kelurahan</label>
                <select name="desa_kelurahan" class="w-full border rounded px-3 py-2" required
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
                    <option value="">-- Pilih Desa/Kelurahan --</option>
                    @foreach ($desaList as $desa)
                        <option value="{{ $desa }}" {{ old('desa_kelurahan') == $desa ? 'selected' : '' }}>
                            {{ $desa }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Alasan Pencetakan</label>
                <select name="alasan" id="alasanSelect" class="w-full border rounded px-3 py-2" required
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
                    <option value="">-- Pilih Alasan --</option>
                    <option value="Pembuatan KTP Baru" {{ old('alasan') == 'baru' ? 'selected' : '' }}>Membuat KTP (Baru)
                    </option>
                    <option value="Pembuatan KTP Rusak" {{ old('alasan') == 'rusak' ? 'selected' : '' }}>Membuat KTP (Rusak)
                    </option>
                    <option value="pembaruan data KTP" {{ old('alasan') == 'pembaruan data' ? 'selected' : '' }}>Membuat KTP
                        (Pembaruan
                        Data)</option>
                    <option value="KTP hilang" {{ old('alasan') == 'hilang' ? 'selected' : '' }}>Membuat KTP (Hilang)
                    </option>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">
                    Upload KK
                    <span id="kkNotice" class="text-xs text-yellow-600 font-medium hidden">(KK terbaru)</span>
                </label>
                <input type="file" name="file_kk" accept="image/*" required
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
            </div>
            <div class="hidden" id="ktpLamaField">
                <label class="block mb-1 font-semibold">Upload KTP Lama</label>
                <input type="file" name="file_ktp_lama" accept="image/*"
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
            </div>
            <div class="hidden" id="suratHilangField">
                <label class="block mb-1 font-semibold">Upload Surat Kehilangan</label>
                <input type="file" name="file_surat_kehilangan" accept="image/*"
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Upload Swafoto</label>
                <input type="file" name="file_swafoto" accept="image/*" capture="user" required
                    @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold"
                @if (isset($stokBlangko) && $stokBlangko <= 0) disabled @endif>
                Ajukan Permintaan
            </button>
        </form>
    </div>
    {{-- <img id="previewSwafoto" class="mt-2 w-32 h-auto rounded shadow hidden" /> --}}
@endsection

@push('scripts')
    <script>
        document.querySelector('input[name="file_swafoto"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('previewSwafoto');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alasanSelect = document.getElementById('alasanSelect');
            const ktpLamaField = document.getElementById('ktpLamaField');
            const suratHilangField = document.getElementById('suratHilangField');
            const kkNotice = document.getElementById('kkNotice');

            function toggleFields() {
                const alasan = alasanSelect.value;

                // Reset fields
                ktpLamaField.classList.add('hidden');
                suratHilangField.classList.add('hidden');
                kkNotice.classList.add('hidden');

                // Logic based on descriptive values
                if (alasan === 'Pembuatan KTP Rusak') {
                    ktpLamaField.classList.remove('hidden');
                }

                if (alasan === 'pembaruan data KTP') {
                    ktpLamaField.classList.remove('hidden');
                    kkNotice.classList.remove('hidden'); // KK terbaru notice
                }

                if (alasan === 'KTP hilang') {
                    suratHilangField.classList.remove('hidden');
                }
            }

            alasanSelect.addEventListener('change', toggleFields);
            toggleFields(); // Initialize on page load
        });
    </script>
@endpush
