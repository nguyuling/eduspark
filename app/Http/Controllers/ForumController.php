<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = ForumPost::query();

        // Search by title or content
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        $posts = $query->latest()->get();

        return view('forum.index', compact('posts'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        ForumPost::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => Auth::id(),
            'author_name' => Auth::user()->name,
            'author_avatar' => Auth::user()->avatar ?? '/images/default-user.png'
        ]);

        return redirect()->route('forum.index')->with('success', 'Post created!');
    }

    public function show($id)
    {
        $post = ForumPost::findOrFail($id);
        return view('forum.show', compact('post'));
    }

    public function edit($id)
    {
        $post = ForumPost::findOrFail($id);
        
        // Only creator can edit
        if (Auth::id() !== $post->author_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('forum.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        // Only creator can update
        if (Auth::id() !== $post->author_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect()->route('forum.index')->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);

        // Only creator can delete
        if (Auth::id() !== $post->author_id) {
            abort(403, 'Unauthorized action.');
        }

        $post->delete();
        return redirect()->route('forum.index')->with('success', 'Post deleted.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        ForumReply::create([
            'post_id' => $id,
            'reply_content' => $request->content,
            'author_name' => Auth::user()->name,
            'author_avatar' => Auth::user()->avatar ?? '/images/default-user.png'
        ]);

        return back()->with('success', 'Reply added!');
    }
}
