<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    
                    {{-- START: LEFT SIDE - MODULE NAVIGATION --}}
                    <ul class="navbar-nav me-auto">
                        @auth
                            {{-- LESSONS & FORUM (Accessible to all authenticated users) --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lessons.index') }}">Lessons</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('forum.index') }}">Forum</a>
                            </li>
                            
                            {{-- ROLE-SPECIFIC MODULES --}}
                            @if (Auth::user()->role === 'teacher')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('teacher.quizzes.index') }}">Quiz</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('performance.teacher_view') }}">Performance</a>
                                </li>
                            @else {{-- Assumes student role --}}
                                <li class="nav-item">
                                    {{-- Note: Student Quiz index route name is student.quizzes.index from web.php --}}
                                    <a class="nav-link" href="{{ route('student.quizzes.index') }}">Quiz</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('games.index') }}">Game</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('performance.student_view') }}">Performance</a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                    {{-- END: LEFT SIDE - MODULE NAVIGATION --}}
                    

                    {{-- START: RIGHT SIDE - AUTHENTICATION LINKS (Login/Logout/Register) --}}
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    {{-- You could add a Profile link here if needed --}}
                                    {{-- <a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a> --}}
                                    
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                    {{-- END: RIGHT SIDE - AUTHENTICATION LINKS --}}

                </div>
            </div>
        </nav>
        
        <main class="py-4">
            @yield('content')
        </main>
        
    </div>
</body>
</html>