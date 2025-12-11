<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReportSeeder extends Seeder
{
    public function run()
    {
        /*
         * Temporarily disable foreign key checks on MySQL so TRUNCATE works.
         * This is safe in development. If you prefer not to toggle FK checks,
         * see the alternative approach (delete child rows first) in the chat.
         */
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate in any order (FKs disabled)
        DB::table('attendances')->truncate();
        DB::table('scores')->truncate();
        DB::table('students')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Sample students
        $students = [
            ['name' => 'Alex Tan'],
            ['name' => 'Siti Nur'],
            ['name' => 'Lee Wei'],
        ];

        foreach ($students as $s) {
            $id = DB::table('students')->insertGetId([
                'name' => $s['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sample scores
            $subjects = ['Math', 'Science', 'English'];
            $dates = ['2025-09-01', '2025-10-01', '2025-11-01'];

            foreach ($dates as $d) {
                foreach ($subjects as $sub) {
                    DB::table('scores')->insert([
                        'student_id' => $id,
                        'subject' => $sub,
                        'score' => rand(50, 100),
                        'date' => $d,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Sample attendance
            $attDates = ['2025-09-01', '2025-09-02', '2025-09-03', '2025-09-04', '2025-09-05'];
            foreach ($attDates as $d) {
                DB::table('attendances')->insert([
                    'student_id' => $id,
                    'date' => $d,
                    'present' => rand(0, 10) > 1 ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Teacher user (use only for development)
        if (\Schema::hasTable('users')) {
            // remove existing teacher@example.com to avoid duplicates (safe in dev)
            DB::table('users')->where('email', 'teacher@example.com')->delete();

            DB::table('users')->insert([
                'name' => 'Teacher Amy',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
