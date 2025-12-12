@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-md">
    <h2 class="text-2xl font-bold mb-4">Login</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2" />
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Login</button>
            <a href="/register" class="text-sm text-blue-600">Register</a>
        </div>
    </form>
</div>
@endsection
