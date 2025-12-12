<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');

            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_name')->default('Demo User');
            $table->string('author_avatar')->default('/images/default-user.png');

            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
    }
};
