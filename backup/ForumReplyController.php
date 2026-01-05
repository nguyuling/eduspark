<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumReplyController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required'
        ]);

        ForumReply::create([
            'post_id' => $postId,
            'author_id' => Auth::id(),
            'content' => $request->content
        ]);

        return back()->with('success', 'Reply posted!');
    }
}
