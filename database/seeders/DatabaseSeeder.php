<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(RolePermissionSeeder::class); // Roles and permissions first
        $this->call(BlogSeeder::class);
        $this->call(DummyPostSeeder::class);
        $this->call(PostDetailTestSeeder::class);
        $this->call(SiteSettingsSeeder::class);
    }
}
