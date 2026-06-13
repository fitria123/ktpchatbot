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
            'phone' => $validated['phone'] ?? '', // phone hanya untuk user
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

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // bisa email, nik, atau name (khusus admin)
            'password' => 'required|string',
        ], [
            'login.required' => 'Email/NIK wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cek user
        $user = User::where('email', $request->login)
            ->orWhere('nik', $request->login)
            // ->orWhere('name', $request->login)
            ->first();

        if ($user) {
            // Tentukan field login sesuai input
            if (filter_var($request->login, FILTER_VALIDATE_EMAIL)) {
                $field = 'email';
            } elseif (is_numeric($request->login)) {
                $field = 'nik';
            }

            // Admin → bisa email, nik, name
            if ($user->role === 'admin') {
                $credentials = [
                    $field => $request->login,
                    'password' => $request->password,
                ];
            } else {
                // User → bisa email atau nik saja
                if ($field === 'name') {
                    return back()->withErrors([
                        'login' => 'User hanya bisa login dengan Email atau NIK.',
                    ])->onlyInput('login');
                }

                $credentials = [
                    $field => $request->login,
                    'password' => $request->password,
                ];
            }

            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                $user = Auth::user();

                // 🚨 Cek status user
                if ($user->status === 'inactive') {
                    Auth::logout();
                    return back()->withErrors([
                        'login' => 'Akun Anda sudah tidak aktif. Silakan hubungi admin.',
                    ])->onlyInput('login');
                }

                if ($user->role === 'admin') {
                    return redirect()->intended('/panel-admin/dashboard');
                } elseif ($user->role === 'user') {
                    return redirect()->intended('/user/dashboard');
                }

                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors([
            'login' => 'Email / NIK atau password salah.',
        ])->onlyInput('login');
    }

    protected function getLoginField($loginInput, $user)
    {
        if ($user->email === $loginInput) {
            return 'email';
        } elseif ($user->nik === $loginInput) {
            return 'nik';
        } elseif ($user->username === $loginInput) {
            return 'username';
        }
        return 'email'; // default fallback
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
