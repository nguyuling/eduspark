<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique(); // unique session identifier
            $table->enum('status', ['active', 'completed', 'abandoned', 'timeout'])->default('active');
            $table->integer('current_question')->default(0);
            $table->integer('score')->default(0);
            $table->integer('time_spent')->default(0); // in seconds
            $table->json('answers')->nullable(); // store user's answers
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};