<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EduSpark')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ⬇️ Place your CSS here — same CSS from your UI (sidebar, cards, etc.) */
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
        }

        *{box-sizing:border-box;}
        body.light{background:var(--bg-light);color:#0b1220;}
        body.dark{background:var(--bg-dark);color:#e6eef8;}

        body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;}

        .app{display:flex;min-height:100vh;gap:28px;padding:28px;}

        .sidebar{width:240px;padding:18px;border-radius:16px;display:flex;flex-direction:column;gap:14px;align-items:center;}
        body.light .sidebar{background:rgba(255,255,255,0.9);}
        body.dark .sidebar{background:#0f1724;}

        .main{flex:1;display:flex;flex-direction:column;gap:18px;}

        @media(max-width:920px){
          .sidebar{display:none;}
          .app{padding:14px;}
        }
    </style>
</head>

<body class="dark">

<div class="app">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <h2 style="font-weight:800;letter-spacing:1px;">EduSpark</h2>
        <nav style="width:100%;display:flex;flex-direction:column;gap:10px;text-align:center;">
            <a href="/" style="text-decoration:none;color:var(--muted);font-weight:600;">Home</a>
            <a href="/performance" style="text-decoration:none;color:var(--accent);font-weight:700;">Performance</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Materials</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Assessments</a>
            <a href="#" style="text-decoration:none;color:var(--muted);font-weight:600;">Forum</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="main">
        @yield('content')
    </main>

</div>

</body>
</html>
