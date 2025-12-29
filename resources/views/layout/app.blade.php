<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'EduSpark')</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
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
html,body{height:100%;margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;}

body.dark{background:var(--bg-dark);color:#e6eef8;}

.app{display:flex;min-height:100vh;padding:28px 0;}

.sidebar{
  width:268px;border-radius:16px;display:flex;
  flex-direction:column;gap:14px;align-items:center;
  backdrop-filter:blur(8px);
  background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.03);
}

.logo{width:110px;margin-bottom:4px;}

.logo-text{display:flex;gap:2px;font-weight:700;font-size:18px;}
.logo-text span{font-weight:700}
.e{color:#1D5DCD}.d{color:#2A9D8F}.u{color:#F4C430}
.S{color:#E63946}.p{color:#1D5DCD}.a{color:#2A9D8F}
.r{color:#F4C430}.k{color:#E63946}

.user-box{
  text-align:center;
  padding:10px;
  width:100%;
  border-top:1px solid rgba(255,255,255,0.05);
  border-bottom:1px solid rgba(255,255,255,0.05);
}

.user-box button{
  background:none;
  border:none;
  color:#fff;
  font-weight:600;
  cursor:pointer;
}

.user-dropdown{
  display:none;
  margin-top:8px;
}

.user-dropdown a{
  display:block;
  color:var(--muted);
  text-decoration:none;
  font-size:14px;
  margin:4px 0;
}

.user-box:hover .user-dropdown{display:block;}

.nav{width:100%;margin-top:6px;}
.nav a{
  display:flex;align-items:center;gap:10px;
  padding:10px 14px;border-radius:12px;
  color:var(--muted);text-decoration:none;font-weight:600;
}
.nav a.active{
  background:linear-gradient(90deg,rgba(106,77,247,0.16),rgba(156,123,255,0.08));
  color:var(--accent);
}

.logout{
  margin-top:auto;
  padding-bottom:14px;
}

.logout button{
  background:none;
  border:none;
  color:#ff8b94;
  font-weight:700;
  cursor:pointer;
}

.main{flex:1;padding:0 20px;}
</style>
</head>

<body class="dark">

<div class="app">

{{-- ================= Sidebar ================= --}}
<aside class="sidebar">

  <img class="logo" src="{{ asset('logo.png') }}" alt="EduSpark">

  <div class="logo-text">
    <span class="e">e</span><span class="d">d</span><span class="u">u</span>
    <span class="S">S</span><span class="p">p</span><span class="a">a</span>
    <span class="r">r</span><span class="k">k</span>
  </div>

  {{-- User Dropdown --}}
  <div class="user-box">
    <button>
      {{ auth()->user()->name }}
    </button>
    <div class="user-dropdown">
      <a href="{{ route('users.show', auth()->id()) }}">View Profile</a>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="nav">
    <a href="{{ route('performance') }}" class="{{ request()->routeIs('performance') ? 'active' : '' }}">
      Dashboard
    </a>

    <a href="{{ route('lesson.index') }}" class="{{ request()->routeIs('lesson.*') ? 'active' : '' }}">
      Materials
    </a>

    <a href="{{ route('student.quizzes.index') }}">
      Assessments
    </a>

    <a href="{{ route('forum.index') }}" class="{{ request()->routeIs('forum.*') ? 'active' : '' }}">
      Forum
    </a>

    <a href="{{ route('games.index') }}">
      Games
    </a>
  </nav>

  {{-- Logout --}}
  <div class="logout">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">🚪 Logout</button>
    </form>
  </div>

</aside>

{{-- ================= Main ================= --}}
<main class="main">
  @yield('content')
</main>

</div>

<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('user-name')) {

        // Remove existing menu (if any)
        const oldMenu = document.getElementById('user-action-menu');
        if (oldMenu) oldMenu.remove();

        const userId = e.target.dataset.userId;

        const menu = document.createElement('div');
        menu.id = 'user-action-menu';
        menu.style.position = 'absolute';
        menu.style.background = '#fff';
        menu.style.color = '#000';
        menu.style.border = '1px solid #ddd';
        menu.style.borderRadius = '6px';
        menu.style.padding = '8px 12px';
        menu.style.zIndex = 9999;
        menu.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';

        menu.innerHTML = `
            <a href="/users/${userId}" style="display:block;margin-bottom:6px;text-decoration:none;">Lihat Profil</a>
            <a href="/messages/${userId}" style="display:block;text-decoration:none;">Hantar Mesej</a>
        `;

        document.body.appendChild(menu);
        menu.style.top = e.pageY + 'px';
        menu.style.left = e.pageX + 'px';

        // Auto close
        setTimeout(() => menu.remove(), 3000);
    }
});
</script>


</body>
</html>
