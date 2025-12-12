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
</head>
<body>
    <div id="app">
        {{-- START: MAIN CONTENT AREA --}}
        <main class="w-100"> 
            @yield('content')
        </main>
        {{-- END: MAIN CONTENT AREA --}}
    </div>
</body>
</html>