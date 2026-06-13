<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Selamat Datang') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_header.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chatbot.css') }}" rel="stylesheet">
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(32px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(32px);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s cubic-bezier(.4, 0, .2, 1) both;
        }

        .animate-fadeOut {
            animation: fadeOut 0.4s cubic-bezier(.4, 0, .2, 1) both;
        }

        /* Tambahan dark mode bubble */
        .chat-bubble-user {
            background: #2563eb;
            color: #fff;
        }

        .dark .chat-bubble-user {
            background: #1e293b;
            color: #e0e7ef;
        }

        .chat-bubble-bot {
            background: #f3f4f6;
            color: #1e293b;
        }

        .dark .chat-bubble-bot {
            background: #334155;
            color: #e0e7ef;
        }

        /* Input dark mode */
        .dark #userInput {
            background: #1e293b;
            color: #e0e7ef;
            border-color: #334155;
        }

        .dark #chatbox {
            background: #1e293b;
            border-color: #334155;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 text-gray-800 transition-colors duration-300">

    <nav class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/logo_header.png') }}" alt="Logo" class="h-8 w-auto">
                <span class="font-bold text-xl text-blue-700 dark:text-blue-200">Layanan KTP</span>
            </div>
            <ul class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <li><a href="#beranda"
                        class="hover:text-blue-600 dark:hover:text-blue-300 text-gray-700 dark:text-gray-100">Beranda</a>
                </li>
                <li><a href="#tentang"
                        class="hover:text-blue-600 dark:hover:text-blue-300 text-gray-700 dark:text-gray-100">Tentang
                        Layanan</a></li>
                <li><a href="#alur"
                        class="hover:text-blue-600 dark:hover:text-blue-300 text-gray-700 dark:text-gray-100">Alur &
                        Syarat</a></li>
                <li><a href="#faq"
                        class="hover:text-blue-600 dark:hover:text-blue-300 text-gray-700 dark:text-gray-100">FAQ /
                        Bantuan</a></li>
                <li><a href="#kontak"
                        class="hover:text-blue-600 dark:hover:text-blue-300 text-gray-700 dark:text-gray-100">Kontak</a>
                </li>
            </ul>
            <div class="flex items-center space-x-2">
                {{-- Dark Mode Switch --}}
                <button id="darkModeToggle"
                    class="bg-white/80 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full p-2 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg id="icon-sun" class="w-6 h-6 text-yellow-500 dark:hidden" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5" />
                        <path
                            d="M12 1v2m0 18v2m11-11h-2M3 12H1m16.95 6.95l-1.414-1.414M6.343 6.343L4.929 4.929m12.02 0l-1.414 1.414M6.343 17.657l-1.414 1.414" />
                    </svg>
                    <svg id="icon-moon" class="w-6 h-6 text-blue-400 hidden dark:inline" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
                    </svg>
                </button>
                <a href="{{ route('login') }}"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 transition">
                    Login
                </a>
            </div>
        </div>
    </nav>

    {{-- Konten --}}
    <main>
        @yield('content')
    </main>

    {{-- Chatbot --}}
    <div id="chatbot" class="fixed bottom-6 right-6 z-50">
        <div id="chatbox"
            class="hidden bg-white dark:bg-gray-800 border dark:border-gray-700 shadow-2xl rounded-2xl w-80 max-w-full p-0 flex flex-col transition-colors duration-300">
            <div
                class="flex justify-between items-center px-4 py-3 rounded-t-2xl bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-800 dark:to-blue-600">
                <h3 class="font-semibold text-white">Chatbot Bantuan</h3>
                <button onclick="toggleChatbox()"
                    class="text-white text-2xl leading-none hover:text-red-200">&times;</button>
            </div>
            <div id="chat-messages"
                class="flex-1 max-h-72 min-h-[14rem] overflow-y-auto text-sm px-4 py-3 bg-gray-50 dark:bg-gray-900 space-y-2 custom-scrollbar">
            </div>
            <div class="flex gap-1 border-t px-3 py-2 bg-white dark:bg-gray-800 rounded-b-2xl">
                <input type="text" id="userInput"
                    class="flex-1 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 dark:bg-gray-900 dark:text-gray-100"
                    placeholder="Ketik pesan...">
                <button onclick="sendMessage()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-semibold shadow transition">Kirim</button>
            </div>
        </div>
        <button onclick="toggleChatbox()"
            class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-4 rounded-full shadow-2xl text-2xl focus:outline-none focus:ring-2 focus:ring-blue-300">
            💬
        </button>
    </div>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-gray-900 border-t dark:border-gray-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 py-6 text-sm text-gray-600 dark:text-gray-300 text-center">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </footer>

    @stack('scripts')

    <script>
        // Dark mode toggle
        const darkToggle = document.getElementById('darkModeToggle');
        darkToggle?.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
        // Load preferensi dark mode
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>

    <script>
        let intents = [];

        const toggleChatbox = () => {
            const chatbox = document.getElementById('chatbox');
            if (chatbox.classList.contains('hidden')) {
                chatbox.classList.remove('hidden', 'animate-fadeOut');
                chatbox.classList.add('animate-fadeIn');
                setTimeout(() => document.getElementById('userInput').focus(), 100);
            } else {
                chatbox.classList.remove('animate-fadeIn');
                chatbox.classList.add('animate-fadeOut');
                setTimeout(() => chatbox.classList.add('hidden'), 400);
            }
        };

        const loadIntents = async () => {
            if (intents.length > 0) return;
            try {
                const res = await fetch('/chatbot-intents.json');
                const data = await res.json();
                intents = data.intents;
            } catch (e) {
                console.error("Gagal memuat intents:", e);
                intents = [];
            }
        };

        const sendMessage = () => {
            const input = document.getElementById('userInput');
            const msg = input.value.trim();
            if (!msg) return;

            appendMessage("🧑‍💼 Anda", escapeHtml(msg), true);
            input.value = "";

            const response = getBotResponse(msg);
            setTimeout(() => {
                appendMessage("🤖 Bot", response, false);
            }, 400);
        };

        const appendMessage = (sender, text, isUser = false) => {
            const chat = document.getElementById('chat-messages');
            const messageHTML = `
			<div class="flex ${isUser ? 'justify-end' : 'justify-start'}">
				<div class="${isUser ? 'chat-bubble-user' : 'chat-bubble-bot'} px-3 py-2 rounded-lg mb-1 max-w-[75%]">
					<div class="font-semibold text-xs mb-1 ${isUser ? 'text-blue-100 dark:text-blue-200' : 'text-blue-700 dark:text-blue-200'}">${sender}</div>
					<div>${text}</div>
				</div>
			</div>
		`;
            chat.insertAdjacentHTML('beforeend', messageHTML);
            chat.scrollTop = chat.scrollHeight;
        };

        const getBotResponse = (userInput) => {
            const input = userInput.toLowerCase();
            let bestMatch = {
                score: 0,
                responses: []
            };

            for (const intent of intents) {
                for (const pattern of intent.patterns) {
                    const score = similarity(input, pattern.toLowerCase());
                    if (score > bestMatch.score) {
                        bestMatch = {
                            score,
                            responses: intent.responses
                        };
                    }
                }
            }

            if (bestMatch.score >= 0.7 && bestMatch.responses.length > 0) {
                return bestMatch.responses[Math.floor(Math.random() * bestMatch.responses.length)];
            }

            // fallback jika ada intent dengan tag 'tidak_dikenali'
            const fallback = intents.find(i => i.tag === 'tidak_dikenali');
            if (fallback && fallback.responses.length > 0) {
                return fallback.responses[Math.floor(Math.random() * fallback.responses.length)];
            }

            return "Maaf, saya belum memahami pertanyaan itu. Silakan coba dengan kata lain atau hubungi admin.";
        };

        const similarity = (s1, s2) => {
            let longer = s1.length > s2.length ? s1 : s2;
            let shorter = s1.length > s2.length ? s2 : s1;
            if (longer.length === 0) return 1.0;
            return (longer.length - editDistance(longer, shorter)) / parseFloat(longer.length);
        };

        const editDistance = (s1, s2) => {
            s1 = s1.toLowerCase();
            s2 = s2.toLowerCase();

            const costs = Array(s2.length + 1).fill(0);
            for (let i = 0; i <= s1.length; i++) {
                let lastValue = i;
                for (let j = 0; j <= s2.length; j++) {
                    if (i === 0)
                        costs[j] = j;
                    else if (j > 0) {
                        let newValue = costs[j - 1];
                        if (s1.charAt(i - 1) !== s2.charAt(j - 1)) {
                            newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                        }
                        costs[j - 1] = lastValue;
                        lastValue = newValue;
                    }
                }
                if (i > 0) costs[s2.length] = lastValue;
            }
            return costs[s2.length];
        };

        const escapeHtml = (text) => {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        };

        document.addEventListener('DOMContentLoaded', () => {
            loadIntents();
            document.getElementById('userInput').addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });
        });
    </script>

</body>

</html>