<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DirectMessage;
use Illuminate\Http\Request;
use Auth;

class DirectMessageController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $users = DirectMessage::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get()
            ->map(function ($msg) use ($userId) {
                return $msg->sender_id == $userId
                    ? $msg->receiver
                    : $msg->sender;
            })
            ->unique('id')
            ->values();

        return response()->json($users);
    }

    public function show(User $user)
    {
        $messages = DirectMessage::where(function ($q) use ($user) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get();

        return view('messages.chat', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        DirectMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'message' => $request->message,
        ]);

        return back();
    }
}
