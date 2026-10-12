<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau password yang dimasukkan salah.',
                ]);
        }

        $user = Auth::user();

        if ($user->status !== 'aktif') {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun Anda sedang nonaktif.',
            ]);
        }

        $request->session()->regenerate();

             if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        }

        if ($user->isApoteker()) {
            return redirect('/apoteker/dashboard');
        }

        // Pelanggan diarahkan ke katalog. Sesuaikan jika route katalog berbeda.
        $roleName = Role::where('id_role', $user->id_role)->value('nama_role');

        if (strtolower($roleName ?? '') === 'pelanggan') {
            return redirect()->route('katalog.index');
        }

        Auth::logout();

        return back()->withErrors([
            'email' => 'Role akun tidak memiliki akses ke halaman ini.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'no_telepon' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $role = Role::whereRaw('LOWER(nama_role) = ?', ['pelanggan'])->first();

        if (!$role) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'Role Pelanggan belum tersedia di tabel roles. Tambahkan role Pelanggan terlebih dahulu.',
                ]);
        }

        DB::transaction(function () use ($validated, $role) {
            $user = new User();
            $user->id_role = $role->id_role;
            $user->nama_staf = $validated['nama_pelanggan'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->status = 'aktif';
            $user->save();

            $pelanggan = new Pelanggan();
            $pelanggan->id_user = $user->id_user;
            $pelanggan->nama_pelanggan = $validated['nama_pelanggan'];
            $pelanggan->no_telepon = $validated['no_telepon'] ?? null;
            $pelanggan->alamat = $validated['alamat'] ?? null;
            $pelanggan->save();
        });

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil! Silakan login menggunakan akun yang baru dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
