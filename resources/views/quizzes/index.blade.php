@extends('layouts.app')

@section('content')

<style>
/* ---------- Theme variables (kept from lessons UI) ---------- */
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

.app { display:flex; min-height:80vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; }

.sidebar{
  width:240px; border-radius:var(--card-radius); padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter: blur(8px) saturate(120%);
  box-shadow: none;
}
body.light .sidebar{
  background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.68));
  border:1px solid rgba(13,18,25,0.05);
}
body.dark .sidebar{
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.03);
}
.logo { width:110px; height:auto; margin-bottom:6px; }

.nav { width:100%; margin-top:10px; padding-top:6px; }
.nav a {
  display:flex; align-items:center; gap:10px;
  padding:10px 12px; border-radius:10px;
  color:var(--muted); text-decoration:none; font-weight:600;
  margin:6px 0; position:relative; font-size:14px;
}
.nav a.active { color:var(--accent); }

.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:16px; margin-bottom:20px; }
.card { border-radius:var(--card-radius); padding:14px 16px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; transition: transform .12s ease, box-shadow .12s ease; background: transparent; }
body.light .card { background:var(--card-light); border:1px solid rgba(11,18,32,0.04); }
body.dark .card  { background:var(--card-dark); border:1px solid rgba(255,255,255,0.03); }
.card .label { font-size:13px; color:var(--muted); font-weight:600; }
.card .value { font-weight:700; font-size:20px; margin-top:6px; }

.panel { border-radius:var(--card-radius); padding:14px; animation: fadeInUp .4s ease; margin-bottom:20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2,6,23,0.18); transition: border-color .2s ease; }
body.light .panel { background: rgba(255,255,255,0.96); }
body.dark .panel  { background:#0f1724; }

.panel:hover { border-color: var(--accent); }

input[type="text"], input[type="date"], textarea, select, input[type="file"] { width:100%; padding:11px 14px; border-radius:8px; border:1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size:14px; outline: none; transition: box-shadow .12s ease, border-color .12s ease, transform .06s ease; resize: vertical; box-sizing: border-box; }
textarea { min-height:84px; line-height:1.45; }

input[type="file"] { padding:8px 12px; border-radius:8px; }

input[type="text"]:focus, textarea:focus, select:focus, input[type="date"]:focus, input[type="file"]:focus { box-shadow: var(--focus-glow); border-color: var(--accent); transform: translateY(-1px); }

::placeholder { color: rgba(255,255,255,0.45); }
label { font-size:13px; color:var(--muted); font-weight:600; display:block; margin-bottom:6px; }
.small-muted { color:var(--muted); font-size:13px; }

button { cursor:pointer; padding:8px 12px; border-radius:10px; border:none; background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; font-weight:700; font-size:14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; box-shadow: 0 6px 18px rgba(8,12,32,0.25); }
button[style*="background:transparent"], .button-outline { background: transparent !important; color: inherit; border: 1px solid rgba(255,255,255,0.06); box-shadow: none; }
button.danger { background: var(--danger); box-shadow: none; }
button:hover { transform: translateY(-3px); opacity:0.98; }
button:active { transform: translateY(-1px); }
.btn-small { padding:6px 10px; font-size:13px; border-radius:8px; }

table { width:100%; border-collapse:collapse; font-size:14px; margin-top:1rem; border: 2px solid #d4c5f9; border-radius: 8px; overflow: hidden; }
thead th { text-align:center; font-weight:700; color:var(--muted); font-size:13px; padding:12px 10px; border-bottom: 2px solid #d4c5f9; background: rgba(212, 197, 249, 0.05); }
tbody td { padding:12px 10px; border-bottom: 1px solid #e5e1f2; vertical-align: middle; background: transparent; border-right: 1px solid #e5e1f2; }
tbody td:last-child { border-right: none; }
tbody tr:last-child td { border-bottom: none; }

tbody tr:hover td { background: rgba(212, 197, 249, 0.08); }

/* Search & Filter input styling - gray borders */
.panel form input[type="text"],
.panel form input[type="email"],
.panel form select {
  border: 2px solid #d1d5db !important;
  background: transparent !important;
  color: inherit;
  padding: 11px 14px !important;
  height: 44px !important;
  box-sizing: border-box;
  border-radius: 8px;
  transition: border-color .2s ease, background .2s ease;
  width: 100% !important;
}

.panel form input[type="text"]:hover,
.panel form input[type="email"]:hover,
.panel form select:hover {
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
}

.panel form input[type="text"]:focus,
.panel form input[type="email"]:focus,
.panel form select:focus {
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
  outline: none;
}

/* Checkbox styling */
input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 20px !important;
  height: 20px !important;
  cursor: pointer;
  border: 2px solid #d1d5db !important;
  border-radius: 4px;
  transition: border-color .2s ease, background .2s ease;
  box-sizing: border-box;
  background: transparent !important;
  accent-color: var(--accent);
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

input[type="checkbox"]::after {
  content: '';
  position: absolute;
  display: none;
  left: 5px;
  top: 1px;
  width: 6px;
  height: 10px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

input[type="checkbox"]:checked::after {
  display: block;
}

input[type="checkbox"]:hover {
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
}

input[type="checkbox"]:focus {
  outline: none;
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
}

input[type="checkbox"]:checked {
  background: var(--accent) !important;
  border-color: var(--accent) !important;
}

@media (max-width:920px){ .sidebar{ display:none; } .app{ padding:14px; } }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }
.hidden { display:none; }
.center { display:flex; align-items:center; justify-content:center; }
</style>

<div class="app">
  <!-- Sidebar -->
  <aside class="sidebar">
    <img src="{{ asset('logo.png') }}" alt="EduSpark logo" class="logo">
    <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
      <span style="color:#1D5DCD;">edu</span><span style="color:#E63946;">Spark</span>
    </div>
    <nav class="nav">
      <a href="{{ route('home') }}">Lessons</a>
      <a href="#">Forum</a>
      <a href="#">Games</a>
      <a href="{{ Auth::user()->role === 'teacher' ? route('teacher.quizzes.index') : route('student.quizzes.index') }}" class="active">Quiz</a>
      <a href="#">Performance</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:center; margin-bottom:20px;">
      <div>
        <div class="title" style="font-weight:700;font-size:20px;">Quiz</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Take quizzes and track your progress</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;">🌙</button>
    </div>

    <!-- Cards (Stats) -->
    <section class="cards">
      <div class="card">
        <div class="label">Available Quizzes</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px;">
            {{ $quizzes->count() }}
          </span>
        </div>
      </div>
    </section>

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Quiz Filter Panel -->
    <section class="panel">
      <h2 style="margin:0 0 10px 0; font-size:18px;">Search Quiz</h2>
      <form method="GET" action="{{ route('student.quizzes.index') }}">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:12px;">
          <div>
            <label>Unique ID</label>
            <input type="text" name="unique_id" value="{{ $filters['unique_id'] ?? '' }}" placeholder="A1b2C3d4">
          </div>
          <div>
            <label>Title</label>
            <input type="text" name="title" value="{{ $filters['title'] ?? '' }}" placeholder="Computer Science">
          </div>
          <div>
            <label>Creator Email</label>
            <input type="email" name="creator_email" value="{{ $filters['creator_email'] ?? '' }}" placeholder="teacher@email.com">
          </div>
          <div>
            <label>Published Date</label>
            <select name="publish_date" style="width:100%;">
              <option value="">All Time</option>
              <option value="today" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'today') selected @endif>Today</option>
              <option value="this_month" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_month') selected @endif>This Month</option>
              <option value="3_months" @if (isset($filters['publish_date']) && $filters['publish_date'] == '3_months') selected @endif>Last 3 Months</option>
              <option value="this_year" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_year') selected @endif>This Year</option>
            </select>
          </div>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="checkbox" id="attempted" name="attempted" value="1" @if (isset($filters['attempted']) && $filters['attempted'] == '1') checked @endif style="width:auto;padding:0;">
            <label for="attempted" style="margin-bottom:0;font-size:14px;">Only show quizzes I have attempted</label>
          </div>
          <div style="margin-left:auto; display:flex; gap:8px;">
            <a href="{{ route('student.quizzes.index') }}" class="btn-small button-outline">Clear Filters</a>
            <button type="submit" class="btn-small">Apply Filters</button>
          </div>
        </div>
      </form>
    </section>

    <!-- Quiz List Table -->
    <section class="panel">
      <h2 style="margin:0 0 12px 0; font-size:18px;">Quizzes Available</h2>
      
      <table>
        <thead>
          <tr>
            <th style="width:58%">Quiz</th>
            <th style="width:20%">Status</th>
            <th style="width:22%">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($quizzes as $quiz)
            @php
              $completedAttempts = $quiz->attempts;
              $attemptsUsed = $completedAttempts->count();
              $isFutureDue = !$quiz->due_at || $quiz->due_at->isFuture();
              $canAttempt = ($attemptsUsed < $quiz->max_attempts) && $isFutureDue;
              $latestAttempt = $completedAttempts->sortByDesc('submitted_at')->first();
              $statusBadge = 'New';
              if ($latestAttempt) $statusBadge = 'Completed';
              elseif ($quiz->due_at && $quiz->due_at->isPast()) $statusBadge = 'Due';
            @endphp
            <tr>
              <td style="width:58%">
                <div style="font-weight:700; margin-bottom:4px;">{{ $quiz->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ $quiz->description }}</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;"><strong>Creator:</strong> {{ $quiz->creator->name ?? 'N/A' }}</span>
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                    <strong>ID:</strong> {{ $quiz->unique_code ?? 'N/A' }}
                    <button type="button" onclick="copyToClipboard('{{ $quiz->unique_code }}', this)" style="background:none; border:none; cursor:pointer; padding:0; color:var(--accent); font-weight:600; font-size:12px;" title="Copy ID">📋</button>
                  </span>
                  @if ($quiz->due_at)
                    <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px; color:{{ $quiz->due_at->isPast() ? 'var(--danger)' : 'var(--success)' }};">
                      <strong>Due on:</strong> {{ $quiz->due_at->format('M d, Y') }}
                    </span>
                  @endif
                </div>
              </td>
              <td style="width:20%; text-align:center;">
                <div style="font-weight:600; font-size:12px; margin-bottom:4px; padding:4px 8px; background:rgba(106,77,247,0.1); border-radius:6px; display:inline-block;">{{ $statusBadge }}</div>
                <div style="font-size:12px; margin-top:4px;">
                  <div style="margin-bottom:2px;">{{ $attemptsUsed }}/{{ $quiz->max_attempts }} attempts</div>
                  @if ($latestAttempt)
                    @php
                      $totalMarks = $quiz->questions->sum('points') ?? 0;
                    @endphp
                    <div style="font-weight:700;">Score: {{ $latestAttempt->score }}/{{ $totalMarks }}</div>
                  @endif
                </div>
              </td>
              <td style="width:22%; text-align:center;">
                @if ($canAttempt)
                  <a href="{{ route('student.quizzes.attempt.start', $quiz->id) }}" style="display:inline-block; padding:10px 18px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
                    {{ $attemptsUsed > 0 ? 'Re-attempt' : 'Start Quiz' }}
                  </a>
                @elseif ($latestAttempt)
                  <a href="{{ route('student.quizzes.result', $latestAttempt->id) }}" style="display:inline-block; padding:10px 18px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;">
                    View Result
                  </a>
                @else
                  <span style="color:var(--danger); font-size:12px; font-weight:600;">Unavailable</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:24px; color:var(--muted);">
                @if (!empty(array_filter($filters)))
                  No quizzes matched your criteria.
                @else
                  There are currently no published quizzes available for you.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </section>
  </main>
</div>

<script>
const body=document.body, toggle=document.getElementById('themeToggle');
function applyTheme(mode){
  if(mode==='light'){body.classList.replace('dark','light');toggle.textContent='☀️';}
  else{body.classList.replace('light','dark');toggle.textContent='🌙';}
}
const saved=localStorage.getItem('theme')||'dark'; applyTheme(saved);
toggle.addEventListener('click',()=>{const next=body.classList.contains('dark')?'light':'dark'; applyTheme(next); localStorage.setItem('theme',next);});

function copyToClipboard(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const originalText = btn.textContent;
    btn.textContent = '✓ Copied!';
    setTimeout(() => { btn.textContent = originalText; }, 1500);
  });
}
</script>

@endsection