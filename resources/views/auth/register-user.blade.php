@extends('layouts.auth')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register User</h2>

            {{-- Alert error umum --}}
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm text-center">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- NIK --}}
                <div>
                    <label class="block mb-1 text-sm text-gray-600">NIK</label>
                    <input maxlength="16" type="text" name="nik" value="{{ old('nik') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan NIK" required>
                    @error('nik')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block mb-1 text-sm text-gray-600">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan Nama Lengkap" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block mb-1 text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan Email yang aktif" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. HP --}}
                <div>
                    <label class="block mb-1 text-sm text-gray-600">No. Whatsapp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan No. Whatsapp yang aktif" required>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block mb-1 text-sm text-gray-600">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" oninput="checkPasswordStrength(this.value)"
                            class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan Password" required>
                        <button type="button" onclick="togglePassword('password', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                            <i class="fa-solid fa-eye text-gray-400"></i>
                        </button>
                    </div>
                    {{-- indikator strength --}}
                    <div class="mt-2">
                        <div class="w-full h-2 bg-gray-200 rounded">
                            <div id="password-strength-bar" class="h-2 rounded transition-all"></div>
                        </div>
                        <p id="password-strength-text" class="text-xs mt-1 text-gray-600"></p>
                    </div>

                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Konfirmasi Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-1 text-sm text-gray-600">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan Kembali Password" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                            <i class="fa-solid fa-eye text-gray-400"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Register
                </button>
            </form>

            <p class="mt-6 text-sm text-center text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
            </p>
        </div>
    </div>
    <script>
        function togglePassword(id, el) {
            const passwordInput = document.getElementById(id);
            const icon = el.querySelector("i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function checkPasswordStrength(password) {
            const bar = document.getElementById("password-strength-bar");
            const text = document.getElementById("password-strength-text");

            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            let strengthText = "";
            let color = "";

            switch (strength) {
                case 0:
                    strengthText = "";
                    color = "transparent";
                    break;
                case 1:
                    strengthText = "Sangat Lemah";
                    color = "red";
                    break;
                case 2:
                    strengthText = "Lemah";
                    color = "orange";
                    break;
                case 3:
                    strengthText = "Sedang";
                    color = "yellow";
                    break;
                case 4:
                    strengthText = "Kuat";
                    color = "lightgreen";
                    break;
                case 5:
                    strengthText = "Sangat Kuat";
                    color = "green";
                    break;
            }

            bar.style.width = (strength * 20) + "%";
            bar.style.backgroundColor = color;
            text.textContent = "Keakuratan Password: " + strengthText;
        }
    </script>
@endsection
