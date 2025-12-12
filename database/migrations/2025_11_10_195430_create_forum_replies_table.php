<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('author_name')->default('Demo User');
            $table->string('author_avatar')->default('/images/default-user.png');
            $table->longText('reply_content'); // matches your model
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('forum_posts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
    }
};
