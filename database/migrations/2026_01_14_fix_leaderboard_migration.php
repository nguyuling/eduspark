<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix: Update all leaderboard entries with correct student classes and time values
        // Using proper PostgreSQL syntax with single quotes for string literals
        DB::table('leaderboard')->update([
            'class' => DB::raw("COALESCE((SELECT class FROM students WHERE students.user_id = leaderboard.user_id), 'Unknown')"),
            'time_taken' => DB::raw('COALESCE(time_taken, 0)')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for data updates
    }
};
