@extends('layouts.app')

@section('content')
<div class="main">
    <div class="card" style="max-width:600px;margin:40px auto;text-align:center;">
        <div class="text-2xl font-bold mb-2">
            👤 {{ $user->name }}
        </div>

        <p class="opacity-70 mb-4">
            Profile page is coming soon 🚧
        </p>

        <div style="margin-top:20px;">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>
        </div>

        <a href="{{ url()->previous() }}"
           style="display:inline-block;margin-top:24px;color:var(--accent);font-weight:600;">
            ← Back
        </a>
    </div>
</div>
@endsection
