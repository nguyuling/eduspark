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
        // We use hasColumn checks to prevent the "Duplicate column name" error (1060)
        // that occurs if the migration has partially run or the column was added previously.
        Schema::table('users', function (Blueprint $table) {
            // Check for 'phone' before adding (as per the error)
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('password');
            }
            
            // Check for 'role' before adding (it was in an earlier migration)
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['student', 'teacher'])->nullable()->after('phone');
            }
            
            // Add the new required fields
            if (!Schema::hasColumn('users', 'district')) {
                $table->string(column: 'district')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'school_code')) {
                $table->string('school_code')->nullable()->after('district');
            }
            if (!Schema::hasColumn('users', 'user_id')) {
                $table->string(column: 'user_id')->nullable()->after('school_code'); // nullable first!
            }
        });

        // === STEP 2: Backfill existing users with temporary user_id ===
        // This runs only on users that don't have a user_id yet (e.g., users from Laravel starter/breeze)
        DB::table('users')->whereNull('user_id')->update([
            // Use COALESCE to keep existing role, district, and school_code if they were set
            'role' => DB::raw("COALESCE(role, 'student')"),
            'district' => DB::raw("COALESCE(district, 'Johor Bahru')"),
            'school_code' => DB::raw("COALESCE(school_code, 'JJB0000')"),
            // Generate unique user_id based on role and id
            'user_id' => DB::raw("CONCAT(IF(COALESCE(role, 'student') = 'teacher', 'G', 'P'), '-', COALESCE(school_code, 'JJB0000'), '-', SUBSTRING(MD5(id), 1, 3))"),
        ]);

        // === STEP 3: Make all new and existing columns NOT NULL and UNIQUE ===
        Schema::table('users', function (Blueprint $table) {
            // Change phone column to be nullable (as per UserController validation)
            $table->string('phone')->nullable()->change();
            
            // Change role column to be non-nullable (it should already exist)
            $table->enum('role', ['student', 'teacher'])->nullable(false)->change();
            
            // Change new columns to be non-nullable
            $table->string('district')->nullable(false)->change();
            $table->string('school_code')->nullable(false)->change();
            
            // Make user_id NOT NULL and UNIQUE
            $table->string('user_id')->nullable(false)->change();
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the unique constraint first
            $table->dropUnique(['user_id']);
            
            // Drop the new columns
            $table->dropColumn(['phone', 'district', 'school_code', 'user_id']);
            
            // Note: The 'role' column is not dropped here, as it was added in a prior migration (2025_11_18_022748)
            // and should be dropped there if rolling back that far. We only drop columns this migration added.
        });
    }
};