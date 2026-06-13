@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Dashboard Panel Admin</h1>
        <p class="text-gray-600">
            Selamat datang, <span class="font-semibold">{{ Auth::user()->name }}</span>! Berikut ringkasan sistem.
        </p>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        {{-- Card: Permintaan Dokumen --}}
        <a href="{{ route('admin.document.index') }}"
            class="group bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-2xl shadow hover:shadow-lg transition-all duration-200 p-6 flex items-center gap-5 hover:scale-[1.03]">
            <div class="bg-blue-600/10 text-blue-600 rounded-xl p-4 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17l4 4 4-4m0-5V3m-8 4v4a4 4 0 004 4h4" />
                </svg>
            </div>
            <div>
                <div class="text-sm text-blue-700 font-semibold mb-1 group-hover:text-blue-900 transition">
                    Permohonan Cetak
                </div>
                <div class="text-4xl font-bold text-blue-700 group-hover:text-blue-900 transition">
                    {{ $documentRequest }}
                </div>
            </div>
        </a>

        {{-- Card: Blangko Tersedia --}}
        <a href="{{ route('admin.blangko.index') }}"
            class="group bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-2xl shadow hover:shadow-lg transition-all duration-200 p-6 flex items-center gap-5 hover:scale-[1.03]">
            <div class="bg-green-600/10 text-green-600 rounded-xl p-4 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8M12 8v8" />
                </svg>
            </div>
            <div>
                <div class="text-sm text-green-700 font-semibold mb-1 group-hover:text-green-900 transition">
                    Blangko Tersedia
                </div>
                <div class="text-4xl font-bold text-green-700 group-hover:text-green-900 transition">
                    {{ $blangkoStock }}
                </div>
            </div>
        </a>

        {{-- Card: Jumlah User --}}
        <a href="{{ route('admin.users.index') }}"
            class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-2xl shadow hover:shadow-lg transition-all duration-200 p-6 flex items-center gap-5 hover:scale-[1.03]">
            <div class="bg-purple-600/10 text-purple-600 rounded-xl p-4 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 11c1.66 0 3-1.34 3-3S13.66 5 12 5 9 6.34 9 8s1.34 3 3 3zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div>
                <div class="text-sm text-purple-700 font-semibold mb-1 group-hover:text-purple-900 transition">
                    Jumlah User
                </div>
                <div class="text-4xl font-bold text-purple-700 group-hover:text-purple-900 transition">
                    {{ $userCount ?? '-' }}
                </div>
            </div>
        </a>
    </div>

    {{-- Filter Chart --}}
    <div class="flex gap-2 ">
        <select id="filter-month" class="border rounded px-2 py-1">
            @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>

        <select id="filter-year" class="border rounded px-2 py-1">
            @foreach (range(now()->year - 5, now()->year) as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Statistik Visual --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        {{-- Line Chart --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 h-[24rem]">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">
                Grafik Pengajuan Dokumen {{ $year }}
            </h2>
            <div class="w-full h-full">
                <canvas id="chart-pengajuan" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Pie Chart --}}
        <div class="bg-white rounded-2xl shadow border border-gray-100 p-6 h-[24rem] flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">Pie Alasan Pengajuan</h2>
                <span class="text-sm text-gray-500">
                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
                </span>
            </div>
            <div class="flex flex-1 items-center justify-center">
                <canvas id="chart-alasan" class="w-full h-full max-w-[18rem] max-h-[18rem]"></canvas>
            </div>
        </div>

    </div>


    {{-- Kalender --}}
    <div class="mt-10 bg-white p-6 rounded-2xl shadow border border-gray-100">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Kalender Hari Ini</h2>
        <div id="today-date" class="mb-4 text-gray-600 font-medium"></div>
        <input type="text" id="dashboard-calendar" class="w-full rounded-lg border-gray-300 shadow-sm">
    </div>

    {{-- Info tambahan --}}
    <div class="mt-10 bg-white p-6 rounded-2xl shadow border border-gray-100">
        <h2 class="text-lg font-semibold mb-2 text-gray-800">Informasi</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
            Panel ini digunakan oleh petugas admin untuk memproses permintaan pencetakan KTP dari user,
            mengelola stok blangko, serta mencatat riwayat pendaftaran termasuk untuk lansia yang datang langsung.
        </p>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 📅 Tampilkan tanggal hari ini
            const today = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('today-date').textContent = today.toLocaleDateString('id-ID', options);

            // 📌 Flatpickr kalender
            flatpickr("#dashboard-calendar", {
                inline: true,
                locale: "id",
                defaultDate: today,
                dateFormat: "Y-m-d",
                clickOpens: false,
                allowInput: false,
            });

            // 📈 Line Chart
            const ctxLine = document.getElementById('chart-pengajuan').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                        'Des'
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: @json($monthlyData),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointBackgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // 🥧 Pie Chart
            const ctxPie = document.getElementById('chart-alasan').getContext('2d');
            let pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: [
                        'Pembuatan KTP Baru',
                        'Pembuatan KTP Rusak',
                        'KTP Hilang',
                        'Pembaruan data KTP'
                    ],
                    datasets: [{
                        data: [
                            {{ $pieData['Pembuatan KTP Baru'] ?? 0 }},
                            {{ $pieData['Pembuatan KTP Rusak'] ?? 0 }},
                            {{ $pieData['KTP Hilang'] ?? 0 }},
                            {{ $pieData['Pembaruan data KTP'] ?? 0 }}
                        ],
                        backgroundColor: ['#2563eb', '#22c55e', '#f59e42', '#a855f7'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                boxWidth: 20
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 20
                        }
                    }
                },
                plugins: [{
                    // 👉 Plugin custom untuk bikin legend 2 kolom
                    id: 'legendSpacing',
                    afterInit(chart) {
                        const legend = chart.legend;
                        if (legend) {
                            legend.options.labels.generateLabels = function(chart) {
                                const original = Chart.overrides.pie.plugins.legend.labels
                                    .generateLabels(chart);
                                // Bagi jadi 2 kolom
                                return original.map((label, i) => ({
                                    ...label,
                                    textAlign: 'left',
                                    datasetIndex: 0
                                }));
                            };
                        }
                    }
                }]
            });

            // 🔄 Filter Pie Chart
            const monthSelect = document.getElementById('filter-month');
            const yearSelect = document.getElementById('filter-year');

            function updatePieChart() {
                const month = monthSelect.value;
                const year = yearSelect.value;

                fetch(`/panel-admin/dashboard/chart-data?month=${month}&year=${year}`)
                    .then(res => res.json())
                    .then(res => {
                        pieChart.data.datasets[0].data = [
                            res.data['Pembuatan KTP Baru'],
                            res.data['Pembuatan KTP Rusak'],
                            res.data['KTP Hilang'],
                            res.data['Pembaruan data KTP']
                        ];
                        pieChart.update();
                    })
                    .catch(err => console.error('Gagal update chart:', err));
            }

            monthSelect.addEventListener('change', updatePieChart);
            yearSelect.addEventListener('change', updatePieChart);
        });
    </script>
@endpush
