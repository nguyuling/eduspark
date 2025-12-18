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

        $classes = ['4 Arif', '4 Bestari', '5 Arif', '5 Bestari'];

        // For every user with role=student, create a students row if missing
        $users = DB::table('users')->where('role', 'student')->get();

        foreach ($users as $u) {
            $exists = DB::table('students')->where(function ($q) use ($u) {
                // Prefer matching by user_id when column exists
                if (\Schema::hasColumn('students', 'user_id')) {
                    $q->where('user_id', $u->id);
                } else {
                    $q->where('name', $u->name);
                }
            })->first();

            if ($exists) {
                // Update class if column exists and value is empty
                if (\Schema::hasColumn('students', 'class')) {
                    $cls = $classes[array_rand($classes)];
                    DB::table('students')->where('id', $exists->id)->update([
                        'class' => $exists->class ?? $cls,
                        'updated_at' => now(),
                    ]);
                }
                continue;
            }

            $cls = $classes[array_rand($classes)];
            $payload = [
                'name' => $u->name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (\Schema::hasColumn('students', 'user_id')) $payload['user_id'] = $u->id;
            if (\Schema::hasColumn('students', 'class')) $payload['class'] = $cls;

            DB::table('students')->insert($payload);
        }
    }
}
