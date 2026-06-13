<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Selamat Datang') - {{ config('app.name') }}</title>
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/chatbot.css') }}" rel="stylesheet">
</head>

<body class="antialiased bg-gray-50 text-gray-800">
	{{-- Navbar --}}
	<nav class="bg-white shadow-md sticky top-0 z-50">
		<div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
			<div class="flex items-center space-x-4">
				{{-- <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-auto"> --}}
				<span class="font-bold text-xl text-blue-700">Layanan KTP</span>
			</div>
			<ul class="hidden md:flex items-center space-x-6 text-sm font-medium">
				<li><a href="#beranda" class="hover:text-blue-600">Beranda</a></li>
				<li><a href="#tentang" class="hover:text-blue-600">Tentang Layanan</a></li>
				<li><a href="#alur" class="hover:text-blue-600">Alur & Syarat</a></li>
				<li><a href="#faq" class="hover:text-blue-600">FAQ / Bantuan</a></li>
				<li><a href="#kontak" class="hover:text-blue-600">Kontak</a></li>
			</ul>
			<div>
				<a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
					Login
				</a>
			</div>
		</div>
	</nav>

	{{-- Konten --}}
	<main>
		@yield('content')
	</main>

	<div id="chatbot" class="fixed bottom-6 right-6 z-50">
		<div id="chatbox" class="hidden bg-white border shadow-2xl rounded-2xl w-80 max-w-full p-0 flex flex-col">
			<div class="flex justify-between items-center px-4 py-3 rounded-t-2xl bg-gradient-to-r from-blue-600 to-blue-400">
				<h3 class="font-semibold text-white">Chatbot Bantuan</h3>
				<button onclick="toggleChatbox()" class="text-white text-2xl leading-none hover:text-red-200">&times;</button>
			</div>
			<div id="chat-messages"
				class="flex-1 max-h-72 min-h-[14rem] overflow-y-auto text-sm px-4 py-3 bg-gray-50 space-y-2 custom-scrollbar"></div>
			<div class="flex gap-1 border-t px-3 py-2 bg-white rounded-b-2xl">
				<input type="text" id="userInput"
					class="flex-1 border border-gray-300 px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300"
					placeholder="Ketik pesan...">
				<button onclick="sendMessage()"
					class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-semibold shadow">Kirim</button>
			</div>
		</div>
		<button onclick="toggleChatbox()"
			class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-4 rounded-full shadow-2xl text-2xl focus:outline-none focus:ring-2 focus:ring-blue-300">
			💬
		</button>
	</div>

	{{-- Footer --}}
	<footer class="bg-white border-t">
		<div class="max-w-7xl mx-auto px-4 py-6 text-sm text-gray-600 text-center">
			&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
		</div>
	</footer>

	@stack('scripts')

	<script>
		function toggleChatbox() {
			const chatbox = document.getElementById('chatbox');
			chatbox.classList.toggle('hidden');
			if (!chatbox.classList.contains('hidden')) {
				setTimeout(() => document.getElementById('userInput').focus(), 100);
			}
		}

		// Session ID unik per user (disimpan di localStorage)
		let sender = localStorage.getItem('rasa_sender');
		if (!sender) {
			sender = 'user-' + Math.random().toString(36).substr(2, 9);
			localStorage.setItem('rasa_sender', sender);
		}

		const sendMessage = async () => {
			const input = document.getElementById('userInput');
			const chat = document.getElementById('chat-messages');
			const msg = input.value.trim();
			if (!msg) return;

			// Tampilkan pesan user
			appendMessage("🧑‍💼 Anda", escapeHtml(msg));
			input.value = "";

			// Tampilkan animasi mengetik
			const typingId = 'typing-indicator';
			chat.innerHTML += `
            <div id="${typingId}" class="flex justify-start">
                <div class="bg-gray-200 text-gray-800 px-3 py-2 rounded-lg mb-1 max-w-[75%] flex items-center gap-2">
                    <div class="font-semibold text-xs text-blue-700">🤖 Bot</div>
                    <div class="typing-dots">
                        <span class="dot bg-gray-400"></span>
                        <span class="dot bg-gray-400"></span>
                        <span class="dot bg-gray-400"></span>
                    </div>
                    <span class="ml-2 text-gray-500">Sedang mengetik...</span>
                </div>
            </div>
        `;
			chat.scrollTop = chat.scrollHeight;

			try {
				const response = await fetch('http://localhost:5005/webhooks/rest/webhook', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						sender: sender,
						message: msg
					})
				});
				const data = await response.json();

				// Hapus animasi mengetik
				const typingElem = document.getElementById(typingId);
				if (typingElem) typingElem.remove();

				// Tampilkan semua balasan bot (karena Rasa bisa kirim lebih dari 1)
				if (Array.isArray(data) && data.length > 0) {
					data.forEach(botMsg => {
						if (botMsg.text) appendMessage("🤖 Bot", escapeHtml(botMsg.text));
					});
				} else {
					appendMessage("🤖 Bot", "Maaf, saya tidak mengerti maksud Anda.");
				}
			} catch (e) {
				const typingElem = document.getElementById(typingId);
				if (typingElem) typingElem.remove();
				appendMessage("🤖 Bot", "Maaf, terjadi kesalahan koneksi ke server chatbot.");
			}
		};

		const appendMessage = (sender, text) => {
			const chat = document.getElementById('chat-messages');
			const isUser = sender.includes('Anda');
			chat.innerHTML += `
            <div class="flex ${isUser ? 'justify-end' : 'justify-start'}">
                <div class="${isUser ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800'} px-3 py-2 rounded-lg mb-1 max-w-[75%]">
                    <div class="font-semibold text-xs mb-1 ${isUser ? 'text-blue-100' : 'text-blue-700'}">${sender}</div>
                    <div>${text}</div>
                </div>
            </div>
        `;
			chat.scrollTop = chat.scrollHeight;
		};

		document.addEventListener('DOMContentLoaded', () => {
			document.getElementById('userInput').addEventListener('keydown', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					sendMessage();
				}
			});
		});

		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, function(m) {
				return map[m];
			});
		}
	</script>
</body>

</html>
