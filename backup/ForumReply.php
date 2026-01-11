<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    protected $fillable = [
        'post_id',
        'reply_content',
        'author_name',
        'author_avatar'
    ];

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }
}
