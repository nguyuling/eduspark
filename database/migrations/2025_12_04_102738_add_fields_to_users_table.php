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
            $table->string('phone')->nullable()->after('password');
            $table->enum('role', ['student', 'teacher'])->nullable()->after('phone');
            $table->string('district')->nullable()->after('role');
            $table->string('school_code')->nullable()->after('district');
            $table->string('user_id')->nullable()->after('school_code'); // nullable first!
        });

        // === STEP 2: Backfill existing users with temporary user_id ===
        // Only if you have existing users (e.g., from Laravel starter/breeze)
        DB::table('users')->whereNull('user_id')->update([
            'role' => DB::raw("COALESCE(role, 'student')"),
            'district' => DB::raw("'Johor Bahru'"),
            'school_code' => DB::raw("'JJB0000'"),
            'user_id' => DB::raw("CONCAT('P-JJB0000-', SUBSTRING(MD5(id), 1, 3))"),
            // e.g., P-JJB0000-a3f (uses first 3 hex of MD5(id) for uniqueness)
        ]);

        // === STEP 3: Make user_id NOT NULL and UNIQUE ===
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            $table->enum('role', ['student', 'teacher'])->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('school_code')->nullable(false)->change();
            $table->string('user_id')->nullable(false)->change();
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropColumn(['phone', 'role', 'district', 'school_code', 'user_id']);
        });
    }
};