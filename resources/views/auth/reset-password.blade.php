@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Reset Password</h2>

            @if (session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Alert error --}}
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email --}}
                <div>
                    <label class="block mb-1 text-sm text-gray-600">Email</label>
                    <input type="email" name="email" value="{{ request()->email ?? old('email') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukan Email Anda" required>
                </div>

                {{-- Password Baru --}}
                <div class="mb-4">
                    <label for="password" class="block mb-1 text-sm text-gray-600">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" oninput="checkPasswordStrength(this.value)"
                            class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukan Password Baru" required>
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
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="block mb-1 text-sm text-gray-600">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Ulangi Password Baru" required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500">
                            <i class="fa-solid fa-eye text-gray-400"></i>
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Reset Password
                </button>
            </form>
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
            text.textContent = strengthText;
        }
    </script>

@endsection
