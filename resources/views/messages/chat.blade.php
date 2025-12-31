@extends('layouts.app')

@section('content')
<div class="panel" style="max-width:600px;margin:auto;">
    <h3>Chat dengan {{ $user->name }}</h3>

    <div style="height:300px;overflow-y:auto;margin:16px 0;">
        @foreach($messages as $msg)
            <div style="margin-bottom:8px;text-align:{{ $msg->sender_id == auth()->id() ? 'right' : 'left' }}">
                <span style="background:#eee;padding:8px 12px;border-radius:8px;display:inline-block;">
                    {{ $msg->message }}
                </span>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('messages.store', $user->id) }}">
        @csrf
        <input type="text" name="message" placeholder="Type message..." style="width:100%;padding:10px;">
    </form>
</div>
@endsection
