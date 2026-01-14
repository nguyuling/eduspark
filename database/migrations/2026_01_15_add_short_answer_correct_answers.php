<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add correct answers for short answer questions
        // Question 4: Byte range
        DB::table('questions')
            ->where('id', 4)
            ->update([
                'correct_answer' => '-128 hingga 127,-128 to 127,-128~127'
            ]);
        
        // Question 5: Int size in bits
        DB::table('questions')
            ->where('id', 5)
            ->update([
                'correct_answer' => '32,32 bit,32bits'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the correct answers
        DB::table('questions')
            ->whereIn('id', [4, 5])
            ->update([
                'correct_answer' => null
            ]);
    }
};
