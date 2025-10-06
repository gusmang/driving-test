<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah sudah ada admin
        if (!User::where('role', 'admin')->exists()) {
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@gmail.com', // ganti sesuai kebutuhan
                'password' => Hash::make('123456'), // ganti password default
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true
            ]);

            $this->command->info('Admin user berhasil dibuat!');
        } else {
            $this->command->info('Admin user sudah ada, tidak dibuat ulang.');
        }
    }
}
