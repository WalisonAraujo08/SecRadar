<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Azuron
        User::firstOrCreate(['email' => env('ADMIN_EMAIL', 'admin@azuron.com.br')], [
            'name'     => 'Admin Azuron',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'SecRadar@2026!')),
            'is_admin' => true,
            'is_active'=> true,
        ]);
    }
}
