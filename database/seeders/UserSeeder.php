<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 0,
        ]);
    
        User::create([
            'name' => 'Cobrador',
            'email' => 'cobrador@example.com',
            'password' => Hash::make('password'),
            'user_type' => 1,
        ]);
    }
}
