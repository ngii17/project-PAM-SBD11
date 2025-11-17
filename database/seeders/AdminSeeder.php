<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk buat akun admin.
     */
    public function run()
    {
        // Cek kalau admin belum ada (berdasarkan email)
        $adminEmail = 'admin@elevenflower.com';
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'nama' => 'Admin Eleven Flower',
                'alamat' => 'Jl. Bunga Puspa No. 11, Jakarta Selatan',
                'no_telepon' => '0812-3456-7890',
                'email' => $adminEmail,
                'password' => Hash::make('password123'),  // Ganti password ini dengan yang aman!
                'roles' => 'admin',
            ]);

            $this->command->info('Akun admin berhasil dibuat: ' . $adminEmail . ' (password: password123)');
        } else {
            $this->command->info('Akun admin sudah ada, skip.');
        }
    }
}