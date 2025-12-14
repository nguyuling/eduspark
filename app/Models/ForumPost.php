<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'author_name',
        'author_avatar',
        'attachment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'post_id');
    }
}
