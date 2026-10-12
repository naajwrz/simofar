<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role Apoteker jika belum ada
        $roleId = DB::table('roles')->insertGetId([
            'nama_role' => 'Apoteker',
            'deskripsi' => 'Pengelola obat dan verifikasi resep'
        ]);

        // 2. Buat Akun Apoteker
        DB::table('users')->insert([
            'id_role'   => $roleId,
            'nama_staf' => 'Najwa Apoteker',
            'email'     => 'apoteker@simofar.com',
            'password'  => Hash::make('password123'),
            'status'    => 'aktif',
        ]);
    }
}