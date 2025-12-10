// resources/views/dashboard.blade.php (MODIFIED TO USE LAYOUT)

@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <div class="max-w-4xl w-full space-y-8 p-10 bg-white rounded-xl shadow-2xl">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-blue-800 tracking-tight sm:text-5xl">
                Welcome, {{ Auth::user()->name }}! 
            </h1>
            {{-- ... rest of card content ... --}}
        </div>
    </div>
</div>
@endsection