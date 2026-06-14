<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function showRegisterFormUser()
    {
        return view('auth.register-user');
    }

    public function showRegisterFormAdmin()
    {
        return view('auth.register-admin');
    }

    public function register(Request $request)
    {
        $isAdmin = $request->routeIs('admin.register.store');

        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nik' => ['required', 'unique:users,nik', 'digits:16'],
            'name' => ['required', 'string', 'max:255'],
        ];

        $messages = [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.digits' => 'NIK Harus Berupa 16 Digit Angka.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
        ];

        // Kalau user biasa, tambahkan validasi phone
        if (!$isAdmin) {
            $rules['phone'] = ['required', 'string', 'regex:/^(0|\+?62)8[0-9]{8,12}$/', 'unique:users,phone'];
            $messages = array_merge($messages, [
                'phone.required' => 'Nomor HP wajib diisi.',
                'phone.regex' => 'Format Nomor HP tidak valid. Gunakan format 08xx atau 62xx (mis. 081234567890).',
                'phone.unique' => 'Nomor HP sudah digunakan.',
            ]);
        }

        // Normalisasi nomor ke format kanonik (62xxxx) sebelum cek unik & simpan
        if (!$isAdmin && $request->filled('phone')) {
            $request->merge(['phone' => FonnteService::normalizePhone($request->phone)]);
        }

        $validated = $request->validate($rules, $messages);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '',
            'password' => Hash::make($validated['password']),
            'role' => $isAdmin ? 'admin' : 'user',
        ]);

        if (!$isAdmin) {
            // user biasa → kirim verifikasi email
            event(new Registered($user));
            Auth::login($user);
            return redirect()->route('verification.notice')
                ->with('success', 'Registrasi berhasil. Silakan cek email Anda untuk verifikasi.');
        }

        // admin → langsung login tanpa verifikasi email
        return redirect()->route('login')->with('success', 'Registrasi admin berhasil. Silakan login.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email/NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // STEP 1: Cari user berdasarkan email atau NIK
        $user = User::where('email', $request->login)
            ->orWhere('nik', $request->login)
            ->first();

        // STEP 2: Kasus 1 - User tidak ditemukan (NIK/Email Belum Terdaftar)
        if (!$user) {
            return back()->withErrors([
                'login' => 'NIK/Email belum terdaftar, silahkan melakukan registrasi terlebih dahulu.',
            ])->onlyInput('login');
        }

        // STEP 3: Kasus 2 - User ditemukan tapi password salah
        if (!Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->filled('remember'))) {
            return back()->withErrors([
                'login' => 'Password yang Anda masukkan salah. Silakan coba lagi.',
            ])->onlyInput('login');
        }

        // STEP 4: Login sukses, regenerate session
        $request->session()->regenerate();
        
        // Ambil user yang sudah login
        $user = Auth::user();

        // STEP 5: Cek status user (inactive)
        if ($user->status === 'inactive') {
            Auth::logout();
            return back()->withErrors([
                'login' => 'Akun Anda sudah tidak aktif. Silakan hubungi admin.',
            ])->onlyInput('login');
        }

        // STEP 6: Redirect berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->intended('/panel-admin/dashboard');
        }

        // Default untuk user biasa
        return redirect()->intended('/user/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    public function edit()
    {
        $user = Auth::user();
        return view('users.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Normalisasi nomor ke format kanonik (62xxxx) sebelum cek unik & simpan
        if ($request->filled('phone')) {
            $request->merge(['phone' => FonnteService::normalizePhone($request->phone)]);
        }

        $request->validate([
            'nik'   => 'required|string|size:16|unique:users,nik,' . $user->id,
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'regex:/^(0|\+?62)8[0-9]{8,12}$/', 'unique:users,phone,' . $user->id],
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah digunakan.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Format nomor HP tidak valid.',
            'phone.unique' => 'Nomor HP sudah digunakan.',
            'current_password.required_with' => 'Password lama wajib diisi jika ingin mengganti password.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->nik   = $request->nik;
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            // Validasi current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
            }

            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.profile.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}