@extends('layouts.admin')

@section('title', 'Edit Stok Blangko')

@section('content')
	<div class="max-w-md mx-auto mt-10 bg-white p-6 rounded-lg shadow">
		<h1 class="text-xl font-semibold mb-4">Edit Stok Blangko KTP Elektronik</h1>

		<form action="{{ route('admin.blangko.update', $blangko->id) }}" method="POST">
			@csrf
			@method('PUT')

			<div class="mb-4">
				<label for="stock" class="block text-sm font-medium text-gray-700">Jumlah Stok</label>
				<input type="number" name="stock" id="stock" value="{{ old('stock', $blangko->stock) }}"
					class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
				@error('stock')
					<p class="text-red-600 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="flex justify-end">
				<a href="{{ route('admin.blangko.index') }}"
					class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2 hover:bg-gray-300">Batal</a>
				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
			</div>
		</form>
	</div>
@endsection
