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
    </style>
</head>
<body class="light">
    <div id="app">
        {{-- Sidebar Component --}}
        @include('components.sidebar')
        
        {{-- Sidebar --}}
        @include('components.sidebar')
            @yield('content')
