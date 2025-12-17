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
        Schema::table('student_answers', function (Blueprint $table) {
            // Add columns to support coding submissions and grading
            $table->longText('submitted_code')->nullable()->after('submitted_text'); // Student's code submission
            $table->longText('code_output')->nullable()->after('submitted_code'); // Output from running the code
            $table->boolean('code_compiled')->nullable()->after('code_output'); // Whether code compiled/ran successfully
            $table->text('compilation_error')->nullable()->after('code_compiled'); // Error message if code didn't compile
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn(['submitted_code', 'code_output', 'code_compiled', 'compilation_error']);
        });
    }
};
