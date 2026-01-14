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
        // Reset sequences for all tables with imported data
        // This fixes the unique constraint violations when creating new records
        
        $tables = [
            'game_scores',
            'quiz_attempts',
            'rewards',
            'messages',
            'leaderboard',
            'forum_posts',
            'forum_replies',
        ];
        
        foreach ($tables as $table) {
            // Get the max ID for the table
            $maxId = DB::table($table)->max('id') ?? 0;
            $nextId = $maxId + 1;
            
            // Reset the sequence to start after the max existing ID
            DB::statement("ALTER SEQUENCE {$table}_id_seq RESTART WITH {$nextId}");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sequences will revert to their original state when the migration is rolled back
    }
};
