@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
	<div class="flex items-center justify-center min-h-screen bg-gray-100 px-4">
		<div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-md">
			<h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Lupa Password</h2>

			{{-- Status sukses kirim email --}}
			@if (session('status'))
				@if (session('status') === __('passwords.sent'))
					<div role="alert"
						class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm text-center">
						📩 Link reset password sudah kami kirim ke email kamu.
					</div>
				@else
					<div role="alert"
						class="mb-4 p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm text-center">
						{{ session('status') }}
					</div>
				@endif
			@endif

			{{-- Error (semua) --}}
			@if ($errors->any())
				<div role="alert" class="mb-4 p-3 rounded-lg bg-red-50 border border-red-300 text-red-700 text-sm">
					<strong class="block mb-1">Terjadi kesalahan:</strong>
					<ul class="list-disc list-inside space-y-1">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<form action="{{ route('password.email') }}" method="POST" class="space-y-4">
				@csrf

				{{-- Email --}}
				<div>
					<label class="block mb-1 text-sm text-gray-600">Email</label>
					<input type="email" name="email" value="{{ request()->email ?? old('email') }}"
						class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-300' : '' }}"
						placeholder="Masukkan email Anda" required>
				</div>

				{{-- Tombol Kirim --}}
				<button type="submit"
					class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
					Kirim Link Reset
				</button>
			</form>

			<p class="mt-6 text-sm text-center text-gray-600">
				<a href="{{ route('login') }}" class="text-blue-600 hover:underline">Kembali ke Login</a>
			</p>
		</div>
	</div>
@endsection
