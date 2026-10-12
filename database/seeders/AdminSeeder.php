<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = DB::table('roles')->whereRaw('LOWER(nama_role) = ?', ['admin'])->value('id_role');

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@simofar.com'],
            [
                'id_role' => $role,
                'nama_staf' => 'Admin SIMOFAR',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
            ]
        );
    }
}