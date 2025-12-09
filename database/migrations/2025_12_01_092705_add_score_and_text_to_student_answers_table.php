<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->integer('score_gained')->default(0)->after('is_correct');
            $table->text('submitted_text')->nullable()->after('score_gained');
        });
    }

    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            //
        });
    }
};