<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => Str::uuid(),
                'nik' => '3329090101010001',
                'name' => 'Admin KTP',
                'email' => 'salmanseptianto0@gmail.com',
                'phone' => '081234567890',
                'password' => Hash::make('Salman123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'nik' => '3329090101010002',
                'name' => 'Adera User',
                'email' => 'salmanseptianto@gmail.com',
                'phone' => '082112345678',
                'password' => Hash::make('Salman123'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
