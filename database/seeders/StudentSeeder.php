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
        DB::statement("DELETE FROM sqlite_sequence WHERE name='students'");

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

        // Map each student to their exact class (user IDs 9-53)
        $studentClassMap = [
            9 => '5 Arif', 10 => '5 Arif', 11 => '4 Arif', 12 => '5 Arif', 13 => '5 Arif',
            14 => '5 Arif', 15 => '5 Arif', 16 => '4 Arif', 17 => '5 Bestari', 18 => '5 Arif',
            19 => '4 Bestari', 20 => '5 Arif', 21 => '4 Bestari', 22 => '4 Arif', 23 => '5 Bestari',
            24 => '5 Arif', 25 => '4 Arif', 26 => '5 Bestari', 27 => '4 Arif', 28 => '4 Bestari',
            29 => '5 Arif', 30 => '5 Bestari', 31 => '4 Arif', 32 => '4 Bestari', 33 => '4 Arif',
            34 => '5 Arif', 35 => '5 Arif', 36 => '4 Bestari', 37 => '5 Arif', 38 => '5 Arif',
            39 => '5 Arif', 40 => '5 Arif', 41 => '4 Arif', 42 => '5 Bestari', 43 => '5 Bestari',
            44 => '5 Bestari', 45 => '5 Arif', 46 => '5 Bestari', 47 => '5 Arif', 48 => '4 Arif',
            49 => '4 Arif', 50 => '5 Bestari', 51 => '4 Arif', 52 => '4 Arif', 53 => '5 Bestari',
        ];

        // Get all students and create records with their assigned classes
        $users = DB::table('users')->where('role', 'student')->orderBy('id')->get();

        foreach ($users as $u) {
            $cls = $studentClassMap[$u->id] ?? '4 Arif'; // Default to 4 Arif if not mapped

            $payload = [
                'id' => $u->id,
                'name' => $u->name,
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

        echo "All students a