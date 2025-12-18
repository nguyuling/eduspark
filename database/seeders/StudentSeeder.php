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

            $cls = $classes[array_rand($classes)];

            if ($exists) {
                // Update class or classroom_id
                $update = ['updated_at' => now()];
                if (\Schema::hasColumn('students', 'class')) {
                    $update['class'] = $exists->class ?? $cls;
                }
                if (\Schema::hasColumn('students', 'classroom_id') && \Schema::hasTable('classrooms')) {
                    $cid = DB::table('classrooms')->where('name', $cls)->value('id') ?? DB::table('classrooms')->value('id');
                    if ($cid) $update['classroom_id'] = $cid;
                }
                DB::table('students')->where('id', $exists->id)->update($update);
                continue;
            }

            // Build payload; if 'id' column is not autoincrement (SQLite edge-case), set explicit next id
            $nextId = null;
            try {
                $maxId = DB::table('students')->max('id');
                $nextId = ($maxId ?? 0) + 1;
            } catch (\Throwable $e) {
                $nextId = null;
            }

            $payload = [
                'name' => $u->name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (\Schema::hasColumn('students', 'user_id')) $payload['user_id'] = $u->id;
            if (\Schema::hasColumn('students', 'class')) $payload['class'] = $cls;
            if (\Schema::hasColumn('students', 'classroom_id') && \Schema::hasTable('classrooms')) {
                $cid = DB::table('classrooms')->where('name', $cls)->value('id') ?? DB::table('classrooms')->value('id');
                if ($cid) $payload['classroom_id'] = $cid;
            }
            if ($nextId) $payload['id'] = $nextId;

            DB::table('students')->insert($payload);
        }
    }
}
