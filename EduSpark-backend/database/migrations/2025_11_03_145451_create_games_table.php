<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['quiz', 'puzzle', 'memory', 'matching', 'adventure', 'coding']);
            $table->string('category');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->integer('max_level')->default(1);
            $table->integer('time_limit')->nullable();
            $table->integer('points_per_question')->default(10);
            $table->string('cover_image')->nullable();
            $table->string('game_file')->nullable();
            $table->json('additional_files')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->integer('version')->default(1);
            $table->boolean('notify_students')->default(false);
            $table->timestamp('last_updated_at')->useCurrent();
            $table->json('game_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
};