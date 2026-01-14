<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure table exists
        if (! \Schema::hasTable('students')) {
            return;
        }

        // Clear existing students
        DB::statement('DELETE FROM students');
        // Reset sequence for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER SEQUENCE students_id_seq RESTART WITH 1");
        }

        $classes = ['4 Arif', '4 Bestari', '5 Arif', '5 Bestari'];

        // Ensure classrooms table has these classes if it exists
        if (\Schema::hasTable('classrooms')) {
            foreach ($classes as $name) {
                $existing = DB::table('classrooms')->where('name', $name)->first();
                if (! $existing) {
                    DB::table('classrooms')->insert([
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Balanced distribution: 4 Arif (11), 4 Bestari (11), 5 Arif (12), 5 Bestari (11)
        // Covers user IDs 9-53 (45 students)
        $studentClassMap = [
            9 => '4 Arif', 10 => '4 Arif', 11 => '4 Arif', 12 => '4 Arif', 13 => '4 Arif',
            14 => '4 Arif', 15 => '4 Arif', 16 => '4 Arif', 17 => '4 Arif', 18 => '4 Arif', 19 => '4 Arif',

            20 => '4 Bestari', 21 => '4 Bestari', 22 => '4 Bestari', 23 => '4 Bestari', 24 => '4 Bestari',
            25 => '4 Bestari', 26 => '4 Bestari', 27 => '4 Bestari', 28 => '4 Bestari', 29 => '4 Bestari', 30 => '4 Bestari',

            31 => '5 Arif', 32 => '5 Arif', 33 => '5 Arif', 34 => '5 Arif', 35 => '5 Arif',
            36 => '5 Arif', 37 => '5 Arif', 38 => '5 Arif', 39 => '5 Arif', 40 => '5 Arif', 41 => '5 Arif', 42 => '5 Arif',

            43 => '5 Bestari', 44 => '5 Bestari', 45 => '5 Bestari', 46 => '5 Bestari', 47 => '5 Bestari',
            48 => '5 Bestari', 49 => '5 Bestari', 50 => '5 Bestari', 51 => '5 Bestari', 52 => '5 Bestari', 53 => '5 Bestari',
        ];

        // Get all students and create records with their assigned classes
        $users = DB::table('users')->where('role', 'student')->orderBy('id')->get();

        foreach ($users as $u) {
            $cls = $studentClassMap[$u->id] ?? '4 Arif'; // Default to 4 Arif if not mapped

            $payload = [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'student_id' => 'STU' . str_pad($u->id, 5, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (\Schema::hasColumn('students', 'user_id')) $payload['user_id'] = $u->id;
            if (\Schema::hasColumn('students', 'class')) $payload['class'] = $cls;
            if (\Schema::hasColumn('students', 'classroom_id') && \Schema::hasTable('classrooms')) {
                $cid = DB::table('classrooms')->where('name', $cls)->value('id');
                if ($cid) $payload['classroom_id'] = $cid;
            }

            DB::table('students')->insert($payload);
        }

        echo "All students assigned to classes successfully.\n";
    }
}
