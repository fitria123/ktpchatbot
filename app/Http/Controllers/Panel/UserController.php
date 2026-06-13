<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|regex:/^[0-9]+$/|digits:16|unique:users,nik',
            'name' => 'required|string|max:255', // <-- unique dihapus
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[0-9]+$/|max:13|unique:users,phone',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'No. HP wajib diisi.',
            'phone.unique' => 'No. HP sudah terdaftar.',
            'phone.max' => 'No. HP maksimal 13 karakter.',
            'phone.regex' => 'No. HP Harus Berupa Angka.',
        ]);

        $defaultPassword = '12345678';

        User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($defaultPassword),
            'role' => 'user',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        // Hanya bisa ubah dari active → inactive
        if ($user->status === 'active') {
            $user->status = 'inactive';
            $user->save();
        }

        return redirect()->back()->with('success', 'Status user berhasil diperbarui.');
    }
}
