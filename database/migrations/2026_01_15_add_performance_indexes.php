<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add performance indexes
     */
    public function up(): void
    {
        // Users table indexes
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!$this->indexExists('users', 'users_email_unique')) {
                    $table->unique('email')->comment('Email lookup for login');
                }
            });
        }

        // Quizzes table indexes
        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                if (!$this->indexExists('quizzes', 'quizzes_teacher_id_index')) {
                    $table->index('teacher_id')->comment('Filter quizzes by teacher');
                }
                if (!$this->indexExists('quizzes', 'quizzes_is_published_index')) {
                    $table->index('is_published')->comment('Filter published quizzes');
                }
                if (!$this->indexExists('quizzes', 'quizzes_created_at_index')) {
                    $table->index('created_at')->comment('Sort quizzes by date');
                }
            });
        }

        // Questions table indexes
        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                if (!$this->indexExists('questions', 'questions_quiz_id_index')) {
                    $table->index('quiz_id')->comment('Get questions for quiz');
                }
                if (!$this->indexExists('questions', 'questions_teacher_id_index')) {
                    $table->index('teacher_id')->comment('Filter by teacher');
                }
            });
        }

        // Options table indexes
        if (Schema::hasTable('options')) {
            Schema::table('options', function (Blueprint $table) {
                if (!$this->indexExists('options', 'options_question_id_index')) {
                    $table->index('question_id')->comment('Get options for question');
                }
                if (!$this->indexExists('options', 'options_is_correct_index')) {
                    $table->index('is_correct')->comment('Find correct answers');
                }
            });
        }

        // Quiz Attempts table indexes
        if (Schema::hasTable('quiz_attempts')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                if (!$this->indexExists('quiz_attempts', 'quiz_attempts_quiz_id_student_id_index')) {
                    $table->index(['quiz_id', 'student_id'])->comment('Find attempts by quiz and student');
                }
                if (!$this->indexExists('quiz_attempts', 'quiz_attempts_student_id_index')) {
                    $table->index('student_id')->comment('Get student attempts');
                }
                if (!$this->indexExists('quiz_attempts', 'quiz_attempts_submitted_at_index')) {
                    $table->index('submitted_at')->comment('Filter submitted attempts');
                }
            });
        }

        // Student Answers table indexes
        if (Schema::hasTable('student_answers')) {
            Schema::table('student_answers', function (Blueprint $table) {
                if (!$this->indexExists('student_answers', 'student_answers_attempt_id_index')) {
                    $table->index('attempt_id')->comment('Get answers for attempt');
                }
                if (!$this->indexExists('student_answers', 'student_answers_question_id_index')) {
                    $table->index('question_id')->comment('Get answers by question');
                }
            });
        }

        // Student Answer Options table indexes
        if (Schema::hasTable('student_answer_options')) {
            Schema::table('student_answer_options', function (Blueprint $table) {
                if (!$this->indexExists('student_answer_options', 'student_answer_options_student_answer_id_index')) {
                    $table->index('student_answer_id')->comment('Get selected options');
                }
                if (!$this->indexExists('student_answer_options', 'student_answer_options_option_id_index')) {
                    $table->index('option_id')->comment('Find answer selections');
                }
            });
        }

        echo "\n✓ Database indexes created for performance optimization\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if needed
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUniqueIfExists('users_email_unique');
            });
        }

        if (Schema::hasTable('quizzes')) {
            Schema::table('quizzes', function (Blueprint $table) {
                $table->dropIndexIfExists('quizzes_teacher_id_index');
                $table->dropIndexIfExists('quizzes_is_published_index');
                $table->dropIndexIfExists('quizzes_created_at_index');
            });
        }

        if (Schema::hasTable('questions')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropIndexIfExists('questions_quiz_id_index');
                $table->dropIndexIfExists('questions_teacher_id_index');
            });
        }

        if (Schema::hasTable('options')) {
            Schema::table('options', function (Blueprint $table) {
                $table->dropIndexIfExists('options_question_id_index');
                $table->dropIndexIfExists('options_is_correct_index');
            });
        }

        if (Schema::hasTable('quiz_attempts')) {
            Schema::table('quiz_attempts', function (Blueprint $table) {
                $table->dropIndexIfExists('quiz_attempts_quiz_id_student_id_index');
                $table->dropIndexIfExists('quiz_attempts_student_id_index');
                $table->dropIndexIfExists('quiz_attempts_submitted_at_index');
            });
        }

        if (Schema::hasTable('student_answers')) {
            Schema::table('student_answers', function (Blueprint $table) {
                $table->dropIndexIfExists('student_answers_attempt_id_index');
                $table->dropIndexIfExists('student_answers_question_id_index');
            });
        }

        if (Schema::hasTable('student_answer_options')) {
            Schema::table('student_answer_options', function (Blueprint $table) {
                $table->dropIndexIfExists('student_answer_options_student_answer_id_index');
                $table->dropIndexIfExists('student_answer_options_option_id_index');
            });
        }
    }

    /**
     * Check if index exists on table
     */
    private function indexExists($table, $index): bool
    {
        $indexes = \DB::select("
            SELECT indexname FROM pg_indexes 
            WHERE tablename = ? AND indexname = ?
        ", [$table, $index]);
        
        return !empty($indexes);
    }
};
