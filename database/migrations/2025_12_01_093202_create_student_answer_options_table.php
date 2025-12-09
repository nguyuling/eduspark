<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_answer_options', function (Blueprint $table) {
            $table->foreignId('student_answer_id')
                  ->constrained() // Assumes your student_answers table is named as such
                  ->onDelete('cascade');
            
            $table->foreignId('option_id')
                  ->constrained() // Assumes your options table is named 'options'
                  ->onDelete('cascade');
            
            // Define the primary key as the combination of the two foreign keys
            $table->primary(['student_answer_id', 'option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_answer_options');
    }
};