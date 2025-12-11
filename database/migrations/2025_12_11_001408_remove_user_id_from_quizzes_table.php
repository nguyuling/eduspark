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
            // 1. CRITICAL: Drop the foreign key constraint first.
            // Laravel usually names it table_column_foreign.
            // You might need to change 'quizzes_user_id_foreign' if your constraint name is different, 
            // but this is the Laravel default.
            $table->dropForeign('quizzes_user_id_foreign');
            
            // 2. Drop the redundant column that was causing the error.
            $table->dropColumn('user_id');

            // 3. Optional Cleanup: If your controller validates 'subject', ensure it exists.
            // Since your controller was validating 'subject', but your Quiz model wasn't showing it, 
            // let's add it back if you want to use it. If you don't use it, you can remove this block.
            // $table->string('subject', 100)->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Reversing the column drop is complex due to the FK, 
            // but for a quick fix, just ensuring you can roll back is often enough.
            // Since this column is problematic, you may not want a rollback unless necessary.
            // Skip the down method for now to focus on the fix.
        });
    }
};