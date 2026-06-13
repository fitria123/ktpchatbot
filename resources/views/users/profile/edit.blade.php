@extends('layouts.user')

@section('title', 'Edit Profile')

@section('content')
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                <!-- Notifikasi sukses -->
                @if (session('success'))
                    <div class="p-4 bg-green-100 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Current User Info -->
                <div class="px-6 py-5 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Informasi Saat Ini</h2>
                    <div class="grid grid-cols-1 gap-y-4 sm:grid-cols-2 gap-x-6">
                        <div>
                            <p class="text-sm text-gray-500">NIK</p>
                            <p class="font-medium">{{ $user->nik }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama Lengkap</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">No Telp</p>
                            <p class="font-medium">{{ $user->phone }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('user.profile.update') }}" method="POST" class="px-6 py-5">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Edit Information</h2>

                    <!-- NIK -->
                    <div class="mb-4" hidden>
                        <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}"
                            class="input-field w-full px-3 py-2 rounded-md bg-gray-100 cursor-not-allowed" hidden>
                        @error('nik')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="input-field w-full px-3 py-2 rounded-md">
                        @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No Telp</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="input-field w-full px-3 py-2 rounded-md">
                        @error('phone')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="input-field w-full px-3 py-2 rounded-md">
                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Change Section -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password (Opsional)</h3>

                        <!-- Current Password -->
                        <div class="mb-4 relative">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password
                                Lama</label>
                            <input type="password" id="current_password" name="current_password"
                                class="input-field w-full px-3 py-2 rounded-md pr-10" placeholder="Masukkan password lama">
                            <span class="password-toggle mt-3" onclick="togglePassword('current_password', this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                            @error('current_password')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-4 relative">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" id="password" name="password"
                                class="input-field w-full px-3 py-2 rounded-md pr-10" placeholder="Masukkan password baru"
                                onkeyup="checkPasswordStrength(this.value)">
                            <span class="password-toggle mt-1" onclick="togglePassword('password', this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                            @error('password')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <div class="password-strength mt-2 rounded" id="passwordStrength"></div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="mb-4 relative">
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="input-field w-full px-3 py-2 rounded-md pr-10"
                                placeholder="Konfirmasi password baru">
                            <span class="password-toggle mt-3" onclick="togglePassword('password_confirmation', this)">
                                <i class="fa-solid fa-eye text-gray-400"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .input-field {
            border: 1px solid #d1d5db;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .password-strength {
            height: 5px;
            width: 0%;
            transition: width 0.5s ease;
        }

        .strength-0 {
            width: 20%;
            background-color: #ef4444;
        }

        .strength-1 {
            width: 40%;
            background-color: #f97316;
        }

        .strength-2 {
            width: 60%;
            background-color: #f59e0b;
        }

        .strength-3 {
            width: 80%;
            background-color: #84cc16;
        }

        .strength-4 {
            width: 100%;
            background-color: #10b981;
        }
    </style>

    <script>
        function togglePassword(id, el) {
            const input = document.getElementById(id);
            const icon = el.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById("passwordStrength");
            let strength = 0;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            strengthBar.className = "password-strength strength-" + strength;
        }
    </script>
@endsection
