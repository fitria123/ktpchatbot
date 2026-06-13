@extends('layouts.admin')

@section('title', 'Tambah Stok Blangko')

@section('content')
	<div class="max-w-lg mx-auto bg-white rounded-xl shadow p-6 mt-8">
		<h2 class="text-xl font-bold mb-4 text-blue-700">Tambah Stok Blangko</h2>

		@if ($errors->any())
			<div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
				<ul class="list-disc list-inside text-sm">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form action="{{ route('admin.blangko.store') }}" method="POST">
			@csrf
			<div class="mb-3">
				<label class="block mb-1 font-semibold">Tanggal</label>
				<input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required value="{{ old('tanggal') }}">
			</div>
			<div class="mb-3">
				<label class="block mb-1 font-semibold">Nomor BAST</label>
				<input type="text" name="no_bast" class="w-full border rounded px-3 py-2" required value="{{ old('no_bast') }}">
			</div>
			<div class="mb-3">
				<label class="block mb-1 font-semibold">Jumlah Blangko</label>
				<input type="number" name="jumlah_blanko" min="1" class="w-full border rounded px-3 py-2" required value="{{ old('jumlah_blanko') }}">
			</div>
			<div class="flex justify-between">
				<a href="{{ route('admin.blangko.index') }}" class="text-blue-600 hover:underline">Kembali</a>
				<button type="submit"
					class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Simpan</button>
			</div>
		</form>
	</div>
@endsection
