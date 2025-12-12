<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    {{-- ADDED: Bootstrap Icons CDN for better module icons in the sidebar --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ADDED: ESSENTIAL STYLES FOR SIDEBAR LAYOUT --}}
    <style>
        .sidebar-container {
            width: 250px; /* Fixed width for the sidebar */
            z-index: 1000;
        }
        .content-offset {
            margin-left: 250px; /* Push main content past the fixed sidebar */
            width: calc(100% - 250px);
            min-height: 100vh; /* Ensure main content is full height */
        }
        .sidebar-link {
            padding: 10px 15px;
            display: block;
            color: #333; /* Default text color */
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f8f9fa; /* Light hover background */
            color: #007bff; /* Primary color on hover */
        }
    </style>
</head>
<body>
    <div id="app" style="display: flex;">
        
        {{-- START: VERTICAL SIDEBAR (250px wide) --}}
        <div class="sidebar-container bg-white shadow-sm border-end vh-100 position-fixed">
            <div class="p-3">
                
                {{-- App Logo/Brand (using original navbar-brand style) --}}
                <a class="navbar-brand d-block mb-4 text-center text-primary fw-bold" href="{{ url('/') }}" style="font-size: 1.5rem;">
                    {{ config('app.name', 'Laravel') }}
                </a>

                <hr class="mb-4">
                
                {{-- MODULE NAVIGATION LIST --}}
                <ul class="nav flex-column">
                    @auth
                        
                        {{-- 1. CORE MODULES --}}
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="{{ route('lessons.index') }}"><i class="bi bi-book me-2"></i> Lessons</a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="{{ route('forum.index') }}"><i class="bi bi-chat-dots me-2"></i> Forum</a>
                        </li>

                        {{-- 2. ROLE-SPECIFIC MODULES --}}
                        @if (Auth::user()->role === 'teacher')
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="{{ route('teacher.quizzes.index') }}"><i class="bi bi-patch-question me-2"></i> Quiz</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="{{ route('performance.teacher_view') }}"><i class="bi bi-bar-chart me-2"></i> Performance</a>
                            </li>
                        @else {{-- Assumes student role --}}
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="{{ route('student.quizzes.index') }}"><i class="bi bi-patch-question me-2"></i>Quiz</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="{{ route('games.index') }}"><i class="bi bi-joystick me-2"></i> Game</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="{{ route('performance.student_view') }}"><i class="bi bi-graph-up me-2"></i> Performance</a>
                            </li>
                        @endif
                        
                        <hr class="my-3">
                        
                        {{-- 3. USER/AUTHENTICATION LINKS --}}
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="{{ route('profile.show') }}"><i class="bi bi-person-circle me-2"></i> {{ Auth::user()->name }}</a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="sidebar-link text-danger" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> {{ __('Logout') }}
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    @else
                        {{-- Display Login/Register if not authenticated --}}
                        <li class="nav-item">
                            <a class="sidebar-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i> {{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="{{ route('register') }}"><i class="bi bi-person-add me-2"></i> {{ __('Register') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
        {{-- END: VERTICAL SIDEBAR --}}


        {{-- START: MAIN CONTENT AREA (Pushed to the right) --}}
        <main class="py-4 content-offset"> 
            @yield('content')
        </main>
        {{-- END: MAIN CONTENT AREA --}}

    </div>
</body>
</html>