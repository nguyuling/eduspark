<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'EduSpark') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-light: #f5f7ff;
            --bg-dark: #071026;
            --card-light: rgba(255, 255, 255, 0.95);
            --card-dark: #0f1724;
            --accent: #6A4DF7;
            --accent-2: #9C7BFF;
            --muted: #98a0b3;
            --success: #2A9D8F;
            --danger: #E63946;
            --yellow: #F4C430;
        }

        /* Reset */
        * {
            box-sizing: border-box;
            transition: all .18s ease;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
        }

        body.light {
            background: var(--bg-light);
            color: #0b1220;
        }

        body.dark {
            background: var(--bg-dark);
            color: #e6eef8;
        }

        /* Layout */
        .app {
            display: flex;
            min-height: 100vh;
            gap: 28px;
            padding: 28px;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            border-radius: 16px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(8px) saturate(120%);
        }

        body.light .sidebar {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.70), rgba(255, 255, 255, 0.65));
            border: 1px solid rgba(13, 18, 25, 0.05);
        }

        body.dark .sidebar {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .logo {
            width: 110px;
            margin-bottom: 6px;
        }

        .brand {
            font-weight: 700;
            font-size: 18px;
        }

        /* Sidebar nav */
        .nav {
            width: 100%;
            margin-top: 10px;
            text-align: center;
        }

        .nav a {
            display: block;
            padding: 10px 12px;
            border-radius: 12px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            margin: 4px 0;
            position: relative;
        }

        .nav a::before {
            content: '';
            position: absolute;
            left: 8px;
            width: 4px;
            top: 6px;
            bottom: 6px;
            background: var(--accent);
            border-radius: 12px;
            transform: scaleY(0);
            transition: transform .18s ease;
        }

        .nav a:hover::before {
            transform: scaleY(1);
        }

        .nav a.active {
            color: var(--accent);
            background: linear-gradient(90deg, rgba(106, 77, 247, 0.08), rgba(156, 123, 255, 0.02));
            font-weight: 700;
        }

        /* Panel & cards (shared styles) */
        .panel {
            border-radius: 14px;
            padding: 14px;
            animation: fadeInUp .45s ease;
        }

        body.light .panel {
            background: rgba(255, 255, 255, 0.95);
        }

        body.dark .panel {
            background: var(--card-dark);
        }

        .cards {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }

        .card {
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        body.light .card {
            background: var(--card-light);
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        body.dark .card {
            background: var(--card-dark);
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .card .label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 700;
        }

        .card .value {
            font-size: 22px;
            font-weight: 700;
            margin-top: 6px;
        }

        .badge-pill {
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            color: white;
        }

        /* small helpers */
        .title {
            font-size: 22px;
            font-weight: 800;
            margin: 0;
        }

        .sub {
            color: var(--muted);
            font-size: 13px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Centered content wrapper */
        .content-wrapper {
            width: 100%;
            max-width: 1140px;
            margin: 0 auto;
        }

        /* Responsive */
        @media (max-width: 920px) {
            .sidebar {
                display: none;
            }
            
            .app {
                padding: 14px;
            }
            
            .content-wrapper {
                padding: 0 6px;
            }
        }
    </style>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="dark">
<div class="app">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <!-- Gantikan dengan logo sebenar anda -->
        <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
        <div class="brand">
            <span style="color:#1D5DCD;">Pen</span><span style="color:#E63946;">gajar</span>
        </div>

        <nav class="nav">
            {{-- Pautan Navigasi --}}
            <a href="{{ Route::has('reports.index') ? route('reports.index') : url('/reports') }}"
               class="{{ request()->is('reports*') ? 'active' : '' }}">Laporan</a>

            <a href="{{ Route::has('materials.index') ? route('materials.index') : url('/materials') }}"
               class="{{ request()->is('materials*') ? 'active' : '' }}">Bahan</a>

            <a href="{{ Route::has('assessments.index') ? route('assessments.index') : url('/assessments') }}"
               class="{{ request()->is('assessments*') ? 'active' : '' }}">Penilaian</a>

            <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}"
               class="{{ request()->is('forum*') ? 'active' : '' }}">Forum</a>

            <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}"
               class="{{ request()->is('games*') ? 'active' : '' }}">Permainan</a>
        </nav>
    </aside>

    {{-- Kawasan kandungan utama --}}
    <main style="flex:1; display:flex; justify-content:center;">
        <div class="content-wrapper">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <div>
                    <h1 class="title">{{ $title ?? 'Papan Pemuka' }}</h1>
                    <div class="sub">{{ $subtitle ?? '' }}</div>
                </div>

                <button id="themeToggle" style="background:none;border:0;color:inherit;font-size:18px;cursor:pointer;">🌙</button>
            </div>

            {{-- Kandungan utama di sini --}}
            <div class="panel">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<script>
    (function() {
        const toggle = document.getElementById('themeToggle');
        const saved = localStorage.getItem('theme') || 'dark';
        document.body.classList.add(saved);

        function applyTheme(mode) {
            document.body.classList.remove('light', 'dark');
            document.body.classList.add(mode);
            toggle.textContent = mode === 'light' ? '☀️' : '🌙';
        }
        applyTheme(saved);

        toggle.addEventListener('click', () => {
            const next = document.body.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem('theme', next);
            applyTheme(next);
        });
    })();
</script>

@yield('scripts')
</body>
</html>