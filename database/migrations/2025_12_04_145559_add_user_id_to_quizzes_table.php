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
        Schema::table('quizzes', function (Blueprint $table) {
            // Adds an unsigned BIGINT equivalent column, with foreign key constraint 
            // linking to the 'id' column on the 'users' table.
            $table->foreignId('user_id')
                  ->after('id') // Place it right after the primary key for neatness
                  ->constrained() // Assumes the foreign table is 'users'
                  ->onDelete('cascade'); // Ensures quizzes are deleted if the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Must drop the foreign key constraint first
            $table->dropForeign(['user_id']);
            // Then drop the column
            $table->dropColumn('user_id');
        });
    }
};