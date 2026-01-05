<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Quizzes table
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_attempts')->default(1);
            $table->dateTime('due_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('unique_code')->unique();
            $table->integer('max_points')->default(0);
            $table->timestamps();
        });

        // Questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('type'); // multiple_choice, true_false, short_answer, checkbox, coding
            $table->integer('points')->default(0);
            $table->longText('coding_template')->nullable();
            $table->longText('coding_full_code')->nullable();
            $table->string('coding_language')->default('java')->nullable();
            $table->longText('coding_expected_output')->nullable();
            $table->text('hidden_line_numbers')->nullable();
            $table->timestamps();
        });

        // Options table
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Quiz Attempts table
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->decimal('score', 8, 2)->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->text('teacher_remark')->nullable();
            $table->timestamps();
        });

        // Student Answers table
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score_gained', 8, 2)->default(0);
            $table->text('submitted_text')->nullable();
            $table->longText('submitted_code')->nullable();
            $table->longText('code_output')->nullable();
            $table->boolean('code_compiled')->default(false);
            $table->text('compilation_error')->nullable();
            $table->timestamps();
        });

        // Student Answer Options table (for checkbox/multiple answer questions)
        Schema::create('student_answer_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_answer_id')->constrained('student_answers')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('options')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answer_options');
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
