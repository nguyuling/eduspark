<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->string('question_type'); // 'multiple_choice', 'true_false', 'fill_blank'
            $table->json('options')->nullable(); // for multiple choice
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->integer('points')->default(10);
            $table->integer('time_limit')->nullable(); // per question time limit
            $table->integer('order')->default(0); // question order in game
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};