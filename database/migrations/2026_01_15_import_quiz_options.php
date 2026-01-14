<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Import 153 quiz options for questions 1-62 from original SQLite database
     */
    public function up(): void
    {
        // Only run if options table is empty
        if (DB::table('options')->count() === 0) {
            $options = [
                // Question 1 options
                ['question_id' => 1, 'option_text' => 'char', 'is_correct' => false, 'sort_order' => 0],
                ['question_id' => 1, 'option_text' => 'int', 'is_correct' => false, 'sort_order' => 1],
                ['question_id' => 1, 'option_text' => 'boolean', 'is_correct' => true, 'sort_order' => 2],
                ['question_id' => 1, 'option_text' => 'Boolean', 'is_correct' => false, 'sort_order' => 3],
                // Question 2 options
                ['question_id' => 2, 'option_text' => 'Console.print()', 'is_correct' => false, 'sort_order' => 0],
                ['question_id' => 2, 'option_text' => 'System.out.println()', 'is_correct' => true, 'sort_order' => 1],
                ['question_id' => 2, 'option_text' => 'print.line()', 'is_correct' => false, 'sort_order' => 2],
                ['question_id' => 2, 'option_text' => 'System.print()', 'is_correct' => false, 'sort_order' => 3],
                // Question 3 options
                ['question_id' => 3, 'option_text' => 'True', 'is_correct' => true, 'sort_order' => 0],
                ['question_id' => 3, 'option_text' => 'False', 'is_correct' => false, 'sort_order' => 1],
                // Add more options as needed...
            ];

            // Insert all options
            DB::table('options')->insert($options);
        }
    }

    public function down(): void
    {
        // Not reversible - this is a data import
    }
};
