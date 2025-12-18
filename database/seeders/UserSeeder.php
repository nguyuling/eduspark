<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing users (keep only the 11 required)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Test User
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => null,
        ]);

        // Create 5 Teachers
        $teachers = [
            [
                'name' => 'Cikgu Ahmad',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Johor Bahru',
                'school_code' => 'JB001',
                'phone' => '+60123456789',
                'user_id' => 'G-JB001-a1b',
            ],
            [
                'name' => 'Cikgu Farah',
                'email' => 'farah@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Kuala Lumpur',
                'school_code' => 'KL001',
                'phone' => '+60187654321',
                'user_id' => 'G-KL001-c2d',
            ],
            [
                'name' => 'Cikgu Ravi',
                'email' => 'ravi@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Selangor',
                'school_code' => 'SG001',
                'phone' => '+60134567890',
                'user_id' => 'G-SG001-e3f',
            ],
            [
                'name' => 'Cikgu Siti',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Penang',
                'school_code' => 'PN001',
                'phone' => '+60145678901',
                'user_id' => 'G-PN001-g4h',
            ],
            [
                'name' => 'Cikgu Budi',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Kedah',
                'school_code' => 'KD001',
                'phone' => '+60156789012',
                'user_id' => 'G-KD001-i5j',
            ],
        ];

        foreach ($teachers as $teacher) {
            User::create($teacher);
        }

        echo "5 Teachers created successfully.\n";

        // Create 5 Students
        $students = [
            [
                'name' => 'Muhammad Ali',
                'email' => 'ali@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Johor Bahru',
                'school_code' => 'JB001',
                'phone' => '+60211111111',
                'user_id' => 'P-JB001-k6l',
            ],
            [
                'name' => 'Nur Aisha',
                'email' => 'aisha@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Kuala Lumpur',
                'school_code' => 'KL001',
                'phone' => '+60212222222',
                'user_id' => 'P-KL001-m7n',
            ],
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Selangor',
                'school_code' => 'SG001',
                'phone' => '+60213333333',
                'user_id' => 'P-SG001-o8p',
            ],
            [
                'name' => 'Ying Ying Chen',
                'email' => 'yingying@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Penang',
                'school_code' => 'PN001',
                'phone' => '+60214444444',
                'user_id' => 'P-PN001-q9r',
            ],
            [
                'name' => 'Zain Zahari',
                'email' => 'zain@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Kedah',
                'school_code' => 'KD001',
                'phone' => '+60215555555',
                'user_id' => 'P-KD001-s0t',
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }

        echo "5 Students created successfully.\n";
    }
}
