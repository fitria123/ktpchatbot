<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | {{ config('app.name') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_header.png') }}">
</head>

<body class="bg-gray-100 min-h-screen font-sans">

    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside id="sidebar"
            class="fixed md:static z-40 left-0 top-0 h-full w-64 bg-white shadow-lg border-r border-gray-200 transition-transform duration-200 -translate-x-full md:translate-x-0">
            <div class="p-4 text-xl font-semibold border-b">
                Panel Admin
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                    {{ request()->routeIs('admin.index') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-blue-50 text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.document.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                    {{ request()->is('panel-admin/dashboard/document*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-blue-50 text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 16h8M8 12h8m-8-4h8M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"></path>
                    </svg>
                    Permohonan Cetak
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                    {{ request()->is('panel-admin/dashboard/users*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-blue-50 text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 010 7.75">
                        </path>
                    </svg>
                    Manajemen User
                </a>
                <a href="{{ route('admin.blangko.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                    {{ request()->is('panel-admin/dashboard/blangko*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-blue-50 text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 3v4M8 3v4M4 11h16"></path>
                    </svg>
                    Stok Blangko
                </a>
                <a href="{{ route('admin.document.takeEktp') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg transition
        			{{ request()->is('panel-admin/dashboard/take-ektp*') ? 'bg-blue-100 text-blue-700 font-semibold' : 'hover:bg-blue-50 text-gray-700' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6"></path>
                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"
                            fill="none"></circle>
                    </svg>
                    Pengambilan e-KTP
                </a>
            </nav>
        </aside>

        {{-- Overlay for mobile --}}
        <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-black bg-opacity-30 hidden md:hidden"></div>

        {{-- Main --}}
        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <div class="md:hidden">
                    {{-- Optional toggle sidebar for mobile --}}
                    <button id="toggleSidebar" class="text-gray-700 focus:outline-none">
                        ☰
                    </button>
                </div>
                <div class="text-lg font-semibold truncate">
                    @yield('title', 'Dashboard')
                </div>
                {{-- Header Menu --}}
                <ul class="flex items-center gap-3">
                    {{-- Notification --}}
                    <li class="relative">
                        <button id="notifDropdownBtn"
                            class="relative focus:outline-none hover:text-blue-700 transition">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            {{-- Badge jumlah notifikasi --}}
                            @php
                                $notifCount = ($blangkoWarning ? 1 : 0) + ($latestRequests->count() > 0 ? 1 : 0);
                            @endphp
                            @if ($notifCount > 0)
                                <span id="notif-badge"
                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $notifCount }}</span>
                            @endif
                        </button>
                        {{-- Dropdown Notifikasi --}}
                        <div id="notifDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-96 max-w-xs bg-white rounded-2xl shadow-lg border border-gray-100 z-50 p-4">
                            <h2 class="text-base font-semibold mb-3 text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" ...></svg>
                                Notifikasi
                            </h2>
                            {{-- Blangko menipis --}}
                            @if ($blangkoWarning)
                                <div
                                    class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 rounded-lg px-3 py-2">
                                    <svg class="w-5 h-5 text-red-500" ...></svg>
                                    <span>Stok blangko menipis! Sisa <b>{{ $blangkoStock }}</b> blangko.</span>
                                </div>
                            @endif
                            {{-- Inbox pengajuan dokumen --}}
                            <div class="max-h-64 overflow-y-auto pr-1">
                                <div class="font-semibold text-gray-700 mb-2">Inbox Pengajuan Dokumen</div>
                                @forelse($latestRequests as $req)
                                    <a href="{{ route('admin.document.detailShow', $req->id) }}"
                                        class="flex items-start gap-2 mb-3 p-2 rounded hover:bg-blue-50 transition cursor-pointer">
                                        <div class="flex-shrink-0 mt-1">
                                            <svg class="w-5 h-5 text-blue-400" ...></svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm text-gray-800 font-semibold">
                                                {{ $req->user->name ?? '-' }}</div>
                                            <div class="text-xs text-gray-500">NIK: {{ $req->nik }}</div>
                                            <div class="text-xs text-gray-500">Alasan: {{ ucfirst($req->alasan) }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="text-gray-400 text-sm">Belum ada pengajuan dokumen baru.</div>
                                @endforelse
                            </div>
                            <a href="{{ route('admin.document.index') }}"
                                class="mt-4 inline-block text-blue-600 hover:underline text-sm font-semibold">Lihat
                                semua pengajuan</a>
                        </div>
                    </li>
                    {{-- User Dropdown --}}
                    <div class="relative">
                        <button id="userDropdownBtn"
                            class="flex items-center gap-2 focus:outline-none hover:text-blue-700 transition">
                            <span
                                class="hidden md:inline text-gray-700 font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="userDropdownMenu"
                            class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50 border border-gray-100">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button
                                    class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                            </form>
                        </div>
                    </div>
                </ul>
            </header>

            {{-- Content --}}
            <main class="p-6 overflow-y-auto flex-1">
                @yield('content')
            </main>
            <footer class="bg-white border-t border-gray-200 py-4 px-6 text-center">
                <p class="text-xs sm:text-sm text-gray-500">
                    &copy; {{ date('Y') }} <span class="font-semibold">Sistem Panel Admin</span>.
                    Dibuat dengan <span class="text-red-500">❤️</span> untuk mempermudah pengelolaan data pencetakan
                    KTP.
                </p>
            </footer>
        </div>
    </div>
    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('toggleSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
        if (sidebarToggle && sidebar && sidebarOverlay) {
            sidebarToggle.addEventListener('click', openSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
        // Tutup sidebar jika klik menu di mobile
        if (sidebar) {
            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) closeSidebar();
                });
            });
        }
        // User dropdown (biarkan seperti sebelumnya)
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        document.addEventListener('click', function(e) {
            if (userDropdownBtn && userDropdownMenu) {
                if (userDropdownBtn.contains(e.target)) {
                    userDropdownMenu.classList.toggle('hidden');
                } else if (!userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            }
        });
    </script>

    <script>
        // Notifikasi dropdown & badge
        const notifDropdownBtn = document.getElementById('notifDropdownBtn');
        const notifDropdownMenu = document.getElementById('notifDropdownMenu');
        const notifBadge = document.getElementById('notif-badge');
        document.addEventListener('click', function(e) {
            if (notifDropdownBtn && notifDropdownMenu) {
                if (notifDropdownBtn.contains(e.target)) {
                    notifDropdownMenu.classList.toggle('hidden');
                    // Hilangkan badge saat dropdown dibuka
                    if (notifBadge && !notifDropdownMenu.classList.contains('hidden')) {
                        notifBadge.style.display = 'none';
                    }
                } else if (!notifDropdownMenu.contains(e.target)) {
                    notifDropdownMenu.classList.add('hidden');
                }
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
