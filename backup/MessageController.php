<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // List all users (except self)
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return response()->json($users);
    }

    // Get conversation between logged-in user and selected user
    public function conversation(User $user)
    {
        $messages = Message::where(function($q) use ($user){
            $q->where('sender_id', Auth::id())
              ->where('receiver_id', $user->id);
        })->orWhere(function($q) use ($user){
            $q->where('sender_id', $user->id)
              ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    // Send message
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($msg);
    }
}
