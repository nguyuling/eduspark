@extends('layouts.app')

@section('content')
<div class="main" style="padding:40px 20px; display:flex; justify-content:center;">
    <div class="card" style="max-width:500px; width:100%; background:#fff; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.08); text-align:center; padding:32px;">
        
        <!-- Profile Icon -->
        <div style="width:100px; height:100px; margin:0 auto 16px; border-radius:50%; background:#6a4df7; display:flex; align-items:center; justify-content:center; font-size:48px; color:white;">
            👤
        </div>

        <!-- User Name -->
        <div style="font-size:1.75rem; font-weight:700; margin-bottom:6px;">
            {{ $user->name }}
        </div>

        <!-- Role -->
        <p style="color:#6b7280; margin-bottom:16px; font-size:0.95rem;">
            {{ ucfirst($user->role) }}
        </p>

        <!-- Phone & Email -->
        <div style="margin-bottom:24px; text-align:left; padding:0 32px;">
            <p style="margin-bottom:8px;"><strong>Phone:</strong> {{ $user->phone ?? '-' }}</p>
            <p style="margin-bottom:8px;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin-bottom:0;"><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>
        </div>

        <!-- Back Button -->
        <a href="{{ url()->previous() }}"
           style="display:inline-block; margin-top:16px; color:#6a4df7; font-weight:600; text-decoration:none; transition:color 0.2s;"
           onmouseover="this.style.color='#4e3ec4';" onmouseout="this.style.color='#6a4df7';">
            ← Back
        </a>

        <!-- Send Message Button -->
        <a href="#"
           style="display:inline-block; margin-top:16px; padding:10px 24px; background:#6a4df7; color:#fff; font-weight:600; border-radius:8px; text-decoration:none; transition:background 0.2s;"
           onmouseover="this.style.background='#4e3ec4';" onmouseout="this.style.background='#6a4df7';">
           Send Message
        </a>

    </div>
</div>
@endsection
