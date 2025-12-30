<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
                if (! Schema::hasColumn('students', 'class')) {
                    $table->string('class')->nullable()->after('name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (Schema::hasColumn('students', 'class')) {
                    $table->dropColumn('class');
                }
                if (Schema::hasColumn('students', 'user_id')) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
