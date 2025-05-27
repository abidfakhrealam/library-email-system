<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@library.edu',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true
        ]);

        User::factory()
            ->count(10)
            ->create([
                'is_admin' => false,
                'is_active' => true
            ]);
    }
}
