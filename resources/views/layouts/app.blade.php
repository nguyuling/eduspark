<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    {{-- Bootstrap Icons CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/pages.css') }}" rel="stylesheet">
    
    <style>
        /* ---------- Theme variables ---------- */
        :root{
          --bg-light:#f5f7ff;
          --bg-dark:#071026;
          --card-light:rgba(255,255,255,0.9);
          --card-dark:#0f1724;
          --accent:#6A4DF7;
          --accent-2:#9C7BFF;
          --muted:#98a0b3;
          --success:#2A9D8F;
          --danger:#E63946;
          --yellow:#F4C430;
          --glass: rgba(255,255,255,0.04);
          --input-bg: rgba(255,255,255,0.02);
          --control-border: rgba(255,255,255,0.08);
          --radius: 10px;
          --card-radius: 14px;
          --focus-glow: 0 6px 20px rgba(106,77,247,0.12);
          --shadow-soft: 0 6px 20px rgba(2,6,23,0.45);
          font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        body.light { background:var(--bg-light); color:#0b1220; }
        body.dark  { background:var(--bg-dark); color:#e6eef8; }

        body { margin:0; padding:0; }

        /* Sidebar styles are defined in components/sidebar.blade.php to avoid conflicts */

        /* Sidebar nav */
        .nav a {
          display:inline-flex; align-items:center; gap:10px;
          padding:8px 12px; border-radius:10px;
          color:#6b7280; text-decoration:none; font-weight:600;
          margin:6px 0; position:relative; font-size:16px;
          transition: all .2s ease-out;
          background: transparent;
          white-space: nowrap;
          z-index: 1;
        }
        .nav a:hover { 
          color:#ffffff !important;
          background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }
        .nav a:hover .nav-icon {
          color:#ffffff !important;
          stroke:#ffffff !important;
        }
        .nav a.active { 
          color:#ffffff !important;
          background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }
        .nav a.active .nav-icon {
          color:#ffffff !important;
          stroke:#ffffff !important;
        }
        .nav a::before {
          content:''; position:absolute; left:6px; width:3px; height:60%;
          background:#fff; border-radius:10px; transform:scaleY(0);
          transition:transform .15s ease-out;
          pointer-events: none;
          z-index: 0;
        }
        .nav a:hover::before { transform:scaleY(1); }
        .nav a.active::before { transform:scaleY(1); }
        /* Ensure main app content doesn't sit under the fixed sidebar
           Calculation: sidebar left (28) + sidebar width (320) + sidebar padding (28*2) + small gap (16)
           Total: 28 + 320 + 56 + 16 = 420px */
        .app {
          margin-left: 420px;
          transition: margin .18s ease;
        }

        @media (max-width:920px){
          .app { margin-left: 0; }
        }
    </style>
</head>
<body class="light">
    <div id="app">
      {{-- Sidebar Component - Only show to authenticated users --}}
      @auth
        @include('components.sidebar')
      @endauth

      {{-- Page Content: wrap in .app so layout accounts for fixed sidebar --}}
      <div class="app">
        @yield('content')
      </div>
    </div>

    <footer class="page-footer">
        © 2025 EduSpark • Belajar • Bermain • Berkembang
    </footer>
    {{-- Render page-specific scripts --}}
    @yield('scripts')
</body>
</html>