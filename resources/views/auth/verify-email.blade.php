@extends('layouts.auth')

@section('title', 'Verify email')

@section('content')
	@if (Auth::user() && Auth::user()->hasVerifiedEmail())
		<script>
			window.location.href = "{{ Auth::user()->isAdmin() ? route('admin.index') : route('user.index') }}";
		</script>
	@else
		<div class="min-h-screen flex items-center justify-center bg-gray-100">
			<div class="max-w-md w-full bg-white p-8 rounded shadow">
				<h2 class="text-2xl font-bold text-gray-800 mb-4">Verifikasi Email</h2>

				@if (session('status') === 'verification-link-sent')
					<div class="mb-4 text-green-600 font-semibold">
						Link verifikasi baru telah dikirim ke email kamu.
					</div>
				@endif

				<p class="mb-4 text-gray-700">
					Sebelum melanjutkan, silakan periksa email kamu dan klik link verifikasi yang kami kirim.
					Jika kamu belum menerima email tersebut,
					klik tombol di bawah ini untuk mengirim ulang.
				</p>

				<form method="POST" action="{{ route('verification.send') }}">
					@csrf
					<button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
						Kirim Ulang Email Verifikasi
					</button>
				</form>
			</div>
		</div>
	@endif
@endsection
