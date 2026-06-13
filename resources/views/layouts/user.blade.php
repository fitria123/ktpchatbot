<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard User')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_header.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen font-sans">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200 shadow-sm">
        <div class="max-w-3xl mx-auto flex justify-between items-center px-6 py-4">
            <div class="text-xl font-extrabold text-blue-700 tracking-tight">
                <a href="{{ route('user.index') }}" class="hover:underline">Sistem Antrian KTP-El</a>
            </div>
            <div class="relative">
                <button id="userDropdownBtn"
                    class="flex items-center gap-2 focus:outline-none hover:text-blue-700 transition">
                    <span class="hidden md:inline text-gray-700 font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="userDropdownMenu"
                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50 border border-gray-100">

                    {{-- Edit Profile --}}
                    <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class=" text-blue-500"></i> Edit Profile
                    </a>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('user.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                            <i class="text-red-500"></i> Logout
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <main class="flex justify-center items-center min-h-[80vh] px-2">
        <div class="w-full max-w-2xl">
            @yield('content')
        </div>
    </main>
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
