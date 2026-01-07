<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DemoUsersSeeder extends Seeder
{
    public function run()
    {
        // Create two demo users if they don't exist
        User::firstOrCreate(
            ['email' => 'demo1@eduspark.local'],
            ['name' => 'Demo User 1', 'password' => bcrypt('password')]
        );

        User::firstOrCreate(
            ['email' => 'demo2@eduspark.local'],
            ['name' => 'Demo User 2', 'password' => bcrypt('password')]
        );
    }
}
