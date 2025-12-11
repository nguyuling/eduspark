<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'EduSpark')</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ✅ Your original CSS pasted cleanly */
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
html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;}

body.light{background:var(--bg-light);color:#0b1220;}
body.dark{background:var(--bg-dark);color:#e6eef8;}

.app{display:flex;min-height:100vh;gap:28px;padding:28px;}

.sidebar{
  width:240px;border-radius:16px;padding:18px;display:flex;
  flex-direction:column;gap:12px;align-items:center;
  backdrop-filter:blur(8px) saturate(120%);
  -webkit-backdrop-filter:blur(8px);
}
body.light .sidebar{
  background:linear-gradient(180deg,rgba(255,255,255,0.70),rgba(255,255,255,0.65));
  border:1px solid rgba(13,18,25,0.05);
}
body.dark .sidebar{
  background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.03);
}

.logo{width:110px;height:auto;display:block;margin-bottom:6px;}

.logo-text{display:flex;gap:2px;font-weight:700;font-size:18px;align-items:center;}
.logo-text .e{color:#1D5DCD}.logo-text .d{color:#2A9D8F}.logo-text .u{color:#F4C430}
.logo-text .S{color:#E63946}.logo-text .p{color:#1D5DCD}.logo-text .a{color:#2A9D8F}
.logo-text .r{color:#F4C430}.logo-text .k{color:#E63946;}

.nav{width:100%;margin-top:10px;}
.nav a{
  display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;
  color:var(--muted);text-decoration:none;font-weight:600;margin:4px 0;
}
.nav a .dot{width:10px;height:10px;border-radius:50%;background:var(--muted);opacity:0.6;}
.nav a.active{
  background:linear-gradient(90deg,rgba(106,77,247,0.16),rgba(156,123,255,0.08));
  color:var(--accent);
  box-shadow:0 8px 24px rgba(106,77,247,0.08);
}
.logout{margin-top:auto;width:100%;text-align:center;}

.main{flex:1;display:flex;flex-direction:column;gap:18px;}

.bottom-tab{
  display:none;position:fixed;bottom:0;left:0;width:100%;height:60px;
  background:var(--card-dark);display:flex;
  justify-content:space-around;align-items:center;
  box-shadow:0 -2px 10px rgba(0,0,0,0.12);z-index:999;
  border-top:1px solid rgba(255,255,255,0.05);
}
.bottom-tab a{
  display:flex;flex-direction:column;align-items:center;
  justify-content:center;color:var(--muted);text-decoration:none;font-size:12px;
}
.bottom-tab a.active{color:var(--accent);}

@media(max-width:920px){
  .sidebar{display:none;}
  .app{padding:14px;}
  .bottom-tab{display:flex;}
}
</style>

</head>
<body class="dark">

<div class="app">

    {{-- ✅ Sidebar --}}
    <aside class="sidebar">
        <img class="logo" src="{{ asset('logo.png') }}" alt="EduSpark logo">

        <div class="logo-text">
          <span class="e">e</span><span class="d">d</span><span class="u">u</span>
          <span class="S">S</span><span class="p">p</span><span class="a">a</span>
          <span class="r">r</span><span class="k">k</span>
        </div>

        <nav class="nav">
            <a href="#" class="active"><span class="dot"></span> Dashboard</a>
            <a href="#"><span class="dot"></span> Materials</a>
            <a href="#"><span class="dot"></span> Assessments</a>
            <a href="#"><span class="dot"></span> Forum</a>
            <a href="#"><span class="dot"></span> Games</a>
        </nav>

        <div class="logout">
            <a href="#" style="color:#ff8b94;font-weight:700;text-decoration:none;">🚪 Logout</a>
        </div>
    </aside>

    {{-- ✅ Main Content --}}
    <main class="main">
        @yield('content')
    </main>

</div>

{{-- ✅ Bottom Nav (Mobile) --}}
<div class="bottom-tab">
  <a href="#" class="active"><span>Dashboard</span></a>
  <a href="#"><span>Materials</span></a>
  <a href="#"><span>Assessments</span></a>
  <a href="#"><span>Forum</span></a>
  <a href="#"><span>Games</span></a>
</div>

</body>
</html>
