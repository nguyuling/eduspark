<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardTables extends Migration
{
    public function up()
    {
        // Check if table exists before creating
        if (!Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('total_points')->default(0);
                $table->integer('weekly_points')->default(0);
                $table->integer('monthly_points')->default(0);
                $table->integer('rank')->nullable();
                $table->integer('weekly_rank')->nullable();
                $table->integer('monthly_rank')->nullable();
                $table->date('last_updated')->nullable();
                $table->timestamps();
                
                $table->unique('user_id');
                $table->index('total_points');
                $table->index('weekly_points');
                $table->index('monthly_points');
                $table->index('rank');
            });
            \Log::info('Leaderboards table created successfully.');
        } else {
            \Log::info('Leaderboards table already exists. Skipping creation.');
        }
    }

    public function down()
    {
        Schema::dropIfExists('leaderboards');
    }
}