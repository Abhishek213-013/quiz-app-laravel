<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@quiz.com',
            'password' => Hash::make('admin123'),
            'phone' => '+1 (555) 123-4567',
            'bio' => 'Administrator of the Quiz Application platform.',
            'role' => 'admin',
        ]);
    }
}