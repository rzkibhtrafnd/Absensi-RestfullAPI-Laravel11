<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@mail.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    User::create([
        'name' => 'Kepala HRD',
        'email' => 'hr@mail.com',
        'password' => Hash::make('password'),
        'role' => 'hr',
        'divisi' => 'HRD',
        'posisi' => 'Kepala HRD',
    ]);

    User::create([
        'name' => 'Pegawai User',
        'email' => 'pegawai@mail.com',
        'password' => Hash::make('password'),
        'role' => 'pegawai',
        'divisi' => 'IT',
        'posisi' => 'Software Engineer',
    ]);
}
}
