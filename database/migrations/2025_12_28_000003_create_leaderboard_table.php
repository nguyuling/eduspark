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
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('username');
            $table->string('class');
            $table->string('game_id');
            $table->integer('score')->default(0);
            $table->integer('time_taken')->nullable(); // Time in seconds
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamps();
            
            $table->index('game_id');
            $table->index('user_id');
            $table->index('class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
