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
        Schema::table('questions', function (Blueprint $table) {
            // Add columns for the new code hide feature
            $table->text('coding_full_code')->nullable()->change(); // Change to full code instead of just template
            $table->text('hidden_line_numbers')->nullable()->after('coding_expected_output'); // Store comma-separated hidden line numbers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['hidden_line_numbers']);
        });
    }
};
