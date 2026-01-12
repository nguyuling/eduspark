<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // List all users (except self) with unread message counts
    public function index()
    {
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
            ->select('id', 'name', 'email')
            ->get()
            ->map(function($user) use ($currentUserId) {
                // Count unread messages from this user to current user
                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->whereNull('read_at')
                    ->count();
                
                // Get the last message timestamp for sorting
                $lastMessage = Message::where(function($q) use ($user, $currentUserId){
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $currentUserId);
                })->orWhere(function($q) use ($user, $currentUserId){
                    $q->where('sender_id', $currentUserId)
                      ->where('receiver_id', $user->id);
                })->latest()->first();
                
                $user->unread_count = $unreadCount;
                $user->last_message_at = $lastMessage ? $lastMessage->created_at : null;
                
                return $user;
            })
            ->sortByDesc(function($user) {
                // Sort by: users with unread messages first, then by last message time
                return [$user->unread_count > 0 ? 1 : 0, $user->last_message_at];
            })
            ->values();

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
        
        // Mark all messages from this user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

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
