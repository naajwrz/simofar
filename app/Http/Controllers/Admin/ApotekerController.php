<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ApotekerController extends Controller
{
    private function idRoleApoteker(): int
    {
        return Role::whereRaw('LOWER(nama_role) = ?', ['apoteker'])->firstOrFail()->id_role;
    }

    public function index()
    {
        $apotekers = User::where('id_role', $this->idRoleApoteker())->orderBy('nama_staf')->get();

        return view('admin.apoteker', compact('apotekers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_staf' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'id_role' => $this->idRoleApoteker(),
            'nama_staf' => $data['nama_staf'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'aktif',
        ]);

        return back()->with('success', 'Akun apoteker berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id_role', $this->idRoleApoteker())->findOrFail($id);

        $data = $request->validate([
            'nama_staf' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id_user, 'id_user')],
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:8',
        ]);

        $user->nama_staf = $data['nama_staf'];
        $user->email = $data['email'];
        $user->status = $data['status'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', 'Data apoteker berhasil diperbarui.');
    }
}