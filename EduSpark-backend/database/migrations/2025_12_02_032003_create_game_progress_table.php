<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->integer('total_sessions')->default(0);
            $table->integer('completed_sessions')->default(0);
            $table->integer('highest_score')->default(0);
            $table->integer('average_score')->default(0);
            $table->integer('total_time_spent')->default(0); // in seconds
            $table->float('completion_percentage')->default(0);
            $table->timestamp('last_played_at')->nullable();
            $table->json('achievements')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_progress');
    }
};