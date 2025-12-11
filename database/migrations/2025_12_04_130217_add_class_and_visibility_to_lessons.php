<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('class_group')->nullable()->after('uploaded_by'); // e.g. "4A"
            $table->enum('visibility', ['public', 'class'])->default('class')->after('class_group');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('class_group')->nullable()->after('email'); // students/teachers
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['class_group', 'visibility']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('class_group');
        });
    }
};
