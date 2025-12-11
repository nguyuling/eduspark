<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'badge', 'points', 'certificate', 'unlockable'
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('points_required')->default(0);
            $table->integer('game_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('criteria')->nullable(); // JSON conditions for earning
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};