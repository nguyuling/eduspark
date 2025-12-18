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
            // Add coding-specific columns
            $table->longText('coding_template')->nullable()->after('type'); // Template code provided to students
            $table->string('coding_language')->default('java')->after('coding_template'); // Programming language (java, python, javascript, etc.)
            $table->longText('coding_expected_output')->nullable()->after('coding_language'); // Expected output for validation
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['coding_template', 'coding_language', 'coding_expected_output']);
        });
    }
};
