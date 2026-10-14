<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            'apoteker' => [
                'role' => 'Apoteker',
                'deskripsi' => 'Pengelola obat dan verifikasi resep',
                'nama_staf' => 'Najwa Apoteker',
                'email' => 'apoteker@simofar.com',
            ],
            'kasir' => [
                'role' => 'Kasir',
                'deskripsi' => 'Petugas transaksi penjualan',
                'nama_staf' => 'Kasir SIMOFAR',
                'email' => 'kasir@simofar.com',
            ],
        ];

        foreach ($accounts as $account) {
            $roleId = DB::table('roles')
                ->whereRaw('LOWER(nama_role) = ?', [strtolower($account['role'])])
                ->value('id_role');

            if (! $roleId) {
                $roleId = DB::table('roles')->insertGetId([
                    'nama_role' => $account['role'],
                    'deskripsi' => $account['deskripsi'],
                ]);
            }

            DB::table('users')->updateOrInsert(
                ['email' => $account['email']],
                [
                    'id_role' => $roleId,
                    'nama_staf' => $account['nama_staf'],
                    'password' => Hash::make('password123'),
                    'status' => 'aktif',
                ]
            );
        }
    }
}
