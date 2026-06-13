@extends('layouts.home')

@section('title', 'Beranda')

@section('content')
    {{-- Hero Section --}}
    <section id="beranda"
        class="bg-gradient-to-br from-blue-50 to-blue-200 dark:from-gray-900 dark:to-gray-800 py-20 transition-colors duration-300 animate-fadeIn">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-blue-800 dark:text-blue-200 mb-4 drop-shadow">Layanan Cetak
                Ulang
                KTP-El</h1>
            <p class="text-gray-700 dark:text-gray-200 text-lg mb-8">Daftar antrean cetak KTP elektronik secara online
                tanpa
                harus datang langsung ke kantor.</p>
            <a href="{{ route('register') }}"
                class="inline-block px-8 py-3 bg-blue-600 text-white rounded-full shadow hover:bg-blue-700 transition font-semibold text-lg">
                Buat Akun Sekarang
            </a>
        </div>
    </section>

    {{-- Tentang --}}
    <section id="tentang" class="py-16 bg-white dark:bg-gray-900 transition-colors duration-300 animate-fadeIn">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-4">Tentang Layanan</h2>
            <p class="text-gray-600 dark:text-gray-200 mb-6">Layanan ini mempermudah warga dalam melakukan permohonan cetak
                KTP Elektronik.</p>
            <div class="flex flex-col md:flex-row justify-center gap-8 mt-8">
                <div
                    class="flex-1 bg-blue-50 dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition animate-fadeIn">
                    <div class="flex justify-center mb-3">
                        <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 11c1.66 0 3-1.34 3-3S13.66 5 12 5 9 6.34 9 8s1.34 3 3 3zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-blue-700 dark:text-blue-300 mb-2">Praktis & Online</h3>
                    <p class="text-gray-600 dark:text-gray-200">Semua proses pendaftaran dan pengecekan status bisa
                        dilakukan dari
                        rumah.</p>
                </div>
                <div
                    class="flex-1 bg-blue-50 dark:bg-gray-800 rounded-xl p-6 shadow hover:shadow-lg transition animate-fadeIn">
                    <div class="flex justify-center mb-3">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-lg text-green-700 dark:text-green-300 mb-2">Transparan</h3>
                    <p class="text-gray-600 dark:text-gray-200">Status permohonan dapat dipantau secara real-time oleh
                        warga.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Alur & Syarat --}}
    <section id="alur"
        class="py-16 bg-gray-50 dark:bg-gray-900 transition-colors duration-300 animate-fadeIn overflow-x-auto">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-blue-700 dark:text-blue-300 mb-6">Alur & Syarat</h2>

            <!-- Flexbox 5 kotak sejajar -->
            <div class="flex flex-nowrap gap-6 justify-between">

                <!-- Kotak 1 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-center min-w-[200px] animate-fadeIn">
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mb-3">
                        <svg class="w-7 h-7 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 17l4 4 4-4m0-5V3m-8 4v4a4 4 0 004 4h4" />
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-center">Melakukan pendaftaran akun dan login pada
                        aplikasi.</p>
                </div>

                <!-- Kotak 2 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-center min-w-[200px] animate-fadeIn">
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mb-3">
                        <svg class="w-7 h-7 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 17l4 4 4-4m0-5V3m-8 4v4a4 4 0 004 4h4" />
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-center">Mengisi formulir permohonan cetak dengan data
                        yang valid.</p>
                </div>

                <!-- Kotak 3 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-center min-w-[200px] animate-fadeIn">
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mb-3">
                        <svg class="w-7 h-7 text-green-600 dark:text-green-300" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-center">Unggah dokumen Kartu Keluarga, KTP-El lama,
                        surat kehilangan (jika hilang), dan swafoto.</p>
                </div>

                <!-- Kotak 4 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-center min-w-[200px] animate-fadeIn">
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full mb-3">
                        <svg class="w-7 h-7 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3" />
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-center">Tunggu persetujuan dari petugas.</p>
                </div>

                <!-- Kotak 5 -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-center min-w-[200px] animate-fadeIn">
                    <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full mb-3">
                        <svg class="w-7 h-7 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-6 0h6" />
                        </svg>
                    </div>
                    <p class="text-gray-700 dark:text-gray-200 text-center">Datang ke kantor untuk pengambilan jika status
                        KTP sudah tercetak.</p>
                </div>

            </div>
        </div>
    </section>




    {{-- FAQ --}}
    <section id="faq" class="py-16 bg-white dark:bg-gray-900 transition-colors duration-300 animate-fadeIn">
        <div class="max-w-5xl mx-auto px-4">
            <h2 class="text-2xl font-bold text-center text-blue-700 dark:text-blue-300 mb-8">FAQ / Bantuan</h2>
            <div class="space-y-4" id="faqAccordion">
                @php
                    $faqs = [
                        ['Q: Apakah layanan ini gratis?', 'Ya, layanan ini tidak dipungut biaya.'],
                        [
                            'Q: Berapa lama prosesnya?',
                            'Proses biasanya memakan waktu 1 hari kerja ketika blangko tersedia. Jika blangko kosong, harap menunggu sampai blangko tersedia lagi.',
                        ],
                        [
                            'Q: Apakah bisa mengajukan tanpa KTP lama?',
                            'Bisa, namun wajib melampirkan surat kehilangan dari kepolisian.',
                        ],
                        [
                            'Q: Bagaimana cara cek status permohonan?',
                            'Login ke akun Anda, lalu klik menu "Lihat Status Permintaan".',
                        ],
                        [
                            'Q: Apakah foto KTP bisa diganti?',
                            'Bisa, jika KTP anda rusak atau hilang dan akan dilakukan pencetakan ulang harap konfirmasikan dengan petugas untuk melakukan foto ulang.',
                        ],
                    ];
                @endphp
                @foreach ($faqs as $faq)
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden animate-fadeIn">
                        <button type="button"
                            class="w-full flex justify-between items-center px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-200 focus:outline-none faq-toggle transition">
                            <span>{{ $faq[0] }}</span>
                            <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div
                            class="faq-answer px-4 pt-4 pb-4 text-gray-600 dark:text-gray-300 hidden bg-gray-50 dark:bg-gray-800">
                            {{ $faq[1] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Kontak --}}
    <section id="kontak"
        class="py-16 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300 animate-fadeIn">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-2xl font-bold text-blue-700 dark:text-blue-300 mb-4">Kontak</h2>
            <p class="text-gray-600 dark:text-gray-200 mb-2">Untuk pertanyaan lebih lanjut, silakan hubungi kami:</p>
            <div class="flex flex-col md:flex-row justify-center items-center gap-4 mt-4">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm0 12a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2zm12-12a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zm0 12a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">WhatsApp:</span>
                    <span class="text-blue-700 dark:text-blue-300 font-bold">0812-xxxx-xxxx</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 12a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V8a4 4 0 00-8 0v4m8 0v4a4 4 0 01-8 0v-4" />
                    </svg>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Email:</span>
                    <span class="text-blue-700 dark:text-blue-300 font-bold">layanan@ktp-el.go.id</span>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.faq-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const answer = btn.parentElement.querySelector('.faq-answer');
                const icon = btn.querySelector('svg');
                if (answer.classList.contains('hidden')) {
                    // Tutup semua yang lain
                    document.querySelectorAll('.faq-answer').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.faq-toggle svg').forEach(svg => svg.classList.remove(
                        'rotate-180'));
                    // Buka yang ini
                    answer.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    answer.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
        });
    </script>
@endpush
