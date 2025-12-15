<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // === STEP 1: Add columns (nullable, no unique yet) ===
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }
            
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'school_code')) {
                $table->string('school_code')->nullable()->after('district');
            }
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->string('user_id')->nullable()->after('school_code');
            }
        });

        // === STEP 2: Backfill existing users (SQLite-compatible) ===
        $users = DB::table('users')->whereNull('user_id')->get();
        foreach ($users as $user) {
            $role = $user->role ?? 'student';
            $school_code = $user->school_code ?? 'JJB0000';
            $prefix = ($role === 'teacher') ? 'G' : 'P';
            $user_id = $prefix . '-' . $school_code . '-' . substr(md5($user->id), 0, 3);
            
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'role' => $role,
                    'district' => $user->district ?? 'Johor Bahru',
                    'school_code' => $school_code,
                    'user_id' => $user_id,
                ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Try to drop unique constraint, but ignore if it doesn't exist (SQLite)
            try {
                // First check if the index exists before dropping
                if (Schema::hasColumn('users', 'user_id')) {
                    $indexExists = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='users' AND name LIKE '%user_id%'");
                    if (!empty($indexExists)) {
                        $table->dropUnique(['user_id']);
                    }
                }
            } catch (\Exception $e) {
                // Index may not exist, that's ok
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('users', 'school_code')) {
                $table->dropColumn('school_code');
            }
            if (Schema::hasColumn('users', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};