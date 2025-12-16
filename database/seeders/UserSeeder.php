<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
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
            ],
            [
                'name' => 'Cikgu Farah',
                'email' => 'farah@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Kuala Lumpur',
                'school_code' => 'KL001',
                'phone' => '+60187654321',
            ],
            [
                'name' => 'Cikgu Ravi',
                'email' => 'ravi@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Selangor',
                'school_code' => 'SG001',
                'phone' => '+60134567890',
            ],
            [
                'name' => 'Cikgu Siti',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Penang',
                'school_code' => 'PN001',
                'phone' => '+60145678901',
            ],
            [
                'name' => 'Cikgu Budi',
                'email' => 'budi@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'district' => 'Kedah',
                'school_code' => 'KD001',
                'phone' => '+60156789012',
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
            ],
            [
                'name' => 'Nur Aisha',
                'email' => 'aisha@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Kuala Lumpur',
                'school_code' => 'KL001',
                'phone' => '+60212222222',
            ],
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Selangor',
                'school_code' => 'SG001',
                'phone' => '+60213333333',
            ],
            [
                'name' => 'Ying Ying Chen',
                'email' => 'yingying@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Penang',
                'school_code' => 'PN001',
                'phone' => '+60214444444',
            ],
            [
                'name' => 'Zain Zahari',
                'email' => 'zain@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'district' => 'Kedah',
                'school_code' => 'KD001',
                'phone' => '+60215555555',
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }

        echo "5 Students created successfully.\n";
    }
}
