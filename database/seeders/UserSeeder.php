<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@blog.test')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@blog.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        if (!User::where('email', 'editor@blog.test')->exists()) {
            User::create([
                'name' => 'Editor',
                'email' => 'editor@blog.test',
                'password' => Hash::make('password'),
                'role' => 'editor',
            ]);
        }
    }
}
