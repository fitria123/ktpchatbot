@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-bold mb-4 text-gray-800">Manajemen User</h2>
            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold shadow">
                + Tambah User
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-blue-100 text-blue-700">
                        <th class="py-2 px-4 text-left">No.</th>
                        <th class="py-2 px-4 text-left">Nama</th>
                        <th class="py-2 px-4 text-left">Email</th>
                        <th class="py-2 px-4 text-left">NIK</th>
                        <th class="py-2 px-4 text-left">No. HP</th>
                        <th class="py-2 px-4 text-left">Tanggal Daftar</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        {{-- <th class="py-2 px-4 text-left">Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $loop->iteration }}</td>
                            <td class="py-2 px-4">{{ $user->name }}</td>
                            <td class="py-2 px-4">{{ $user->email }}</td>
                            <td class="py-2 px-4">{{ $user->nik }}</td>
                            <td class="py-2 px-4">{{ $user->phone }}</td>
                            <td class="py-2 px-4">
                                {{ \Carbon\Carbon::parse($user->created_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}
                            </td>
                            <td class="py-2 px-4">
                                @if ($user->status === 'active')
                                    <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST"
                                        class="deactivate-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            class="btn-deactivate px-3 py-1 rounded text-xs font-semibold bg-green-600 hover:bg-green-700 text-white">
                                            {{ ucfirst($user->status) }}
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                        class="px-3 py-1 rounded text-xs font-semibold bg-red-600 text-white cursor-not-allowed"
                                        disabled>
                                        {{ ucfirst($user->status) }}
                                    </button>
                                @endif
                            </td>
                            {{-- <td class="py-2 px-4">
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                    class="delete-user-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-delete bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                        detail
                                    </button>
                                </form>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-deactivate').forEach(button => {
            button.addEventListener('click', function() {
                let form = this.closest('.deactivate-form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Ubah status user menjadi inactive?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
