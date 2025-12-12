@extends('layouts.app')

@section('content')

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

.app { display:flex; min-height:100vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; }

.sidebar{
  width:240px; border-radius:var(--card-radius); padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter: blur(8px) saturate(120%);
  box-shadow: none;
  position: fixed;
  left: 28px;
  top: 28px;
  height: calc(100vh - 56px);
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

.main { flex:1; margin-left:268px; }

.panel { border-radius:var(--card-radius); padding:20px; animation: fadeInUp .4s ease; margin-bottom:20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2,6,23,0.18); transition: border-color .2s ease; }
body.light .panel { background: rgba(255,255,255,0.96); }
body.dark .panel  { background:#0f1724; }

.panel:hover { border-color: var(--accent); }

.question-card { border-radius:var(--card-radius); padding:18px; margin-bottom:16px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); }
body.light .question-card { background: rgba(255,255,255,0.96); }
body.dark .question-card { background:#0f1724; }

.question-header { font-weight:700; font-size:15px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; }
.question-points { background:rgba(106,77,247,0.1); padding:4px 10px; border-radius:6px; font-size:12px; color:var(--accent); font-weight:600; }
.question-text { font-size:14px; margin-bottom:16px; line-height:1.5; }
.result-badge { display:inline-block; padding:6px 12px; border-radius:6px; font-weight:600; font-size:13px; margin-bottom:12px; }
.result-badge.correct { background:rgba(42,157,143,0.1); color:var(--success); }
.result-badge.incorrect { background:rgba(230,57,70,0.1); color:var(--danger); }

.option-list { list-style:none; padding:0; margin:8px 0; }
.option-list li { padding:8px 12px; margin-bottom:6px; border-radius:6px; font-size:13px; display:flex; align-items:center; gap:8px; }
.option-list li.correct { background:rgba(42,157,143,0.08); border-left:3px solid var(--success); }
.option-list li.incorrect { background:rgba(230,57,70,0.08); border-left:3px solid var(--danger); }
.option-list li.neutral { background:rgba(152,160,179,0.05); border-left:3px solid var(--muted); }

.score-card { background:linear-gradient(135deg, rgba(106,77,247,0.1), rgba(156,123,255,0.05)); border:2px solid #d4c5f9; border-radius:var(--card-radius); padding:24px; text-align:center; margin-bottom:20px; }
.score-title { color:var(--muted); font-size:13px; font-weight:600; margin-bottom:8px; }
.score-value { font-size:32px; font-weight:700; color:var(--accent); }

.remark-card { background:rgba(42,157,143,0.08); border:2px solid rgba(42,157,143,0.3); border-radius:var(--card-radius); padding:16px; margin-bottom:20px; }
.remark-label { color:var(--success); font-weight:600; margin-bottom:8px; }
.remark-text { color:inherit; font-size:14px; line-height:1.5; }

.actions { display:flex; gap:12px; margin-top:28px; justify-content:center; flex-wrap:wrap; }
.actions a { padding:12px 24px; border-radius:10px; text-decoration:none; font-weight:600; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; }
.actions a.btn-back { background:var(--muted); color:#fff; box-shadow: 0 4px 12px rgba(152,160,179,0.3); }
.actions a.btn-back:hover { transform:translateY(-2px); box-shadow: 0 6px 16px rgba(152,160,179,0.4); }
.actions a.btn-retry { background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; box-shadow: 0 4px 12px rgba(106,77,247,0.3); }
.actions a.btn-retry:hover { transform:translateY(-2px); box-shadow: 0 6px 16px rgba(106,77,247,0.4); }

@media (max-width:920px){
  .sidebar{ display:none; }
  .main { margin-left:0; }
  .app{ padding:14px; gap:14px; }
}

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }
.hidden { display:none; }
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

  <!-- Main Content -->
  <main class="main">
    <div style="display:flex;justify-content:space-between;align-items:center; margin-bottom:24px;">
      <div>
        <div style="font-weight:700;font-size:24px;">{{ $attempt->quiz->title }}</div>
        <div style="color:var(--muted);font-size:13px;margin-top:4px;">Quiz Results</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;font-size:24px;">🌙</button>
    </div>

    <!-- Score Summary Card -->
    <div class="score-card">
      <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:0;">
        <div>
          <div class="score-title">Student</div>
          <div style="font-size:16px; font-weight:700;">{{ $attempt->student->name }}</div>
        </div>
        <div>
          <div class="score-title">Attempt</div>
          <div style="font-size:16px; font-weight:700;">{{ $attempt->attempt_number }} of {{ $attempt->quiz->max_attempts }}</div>
        </div>
        <div>
          <div class="score-title">Submitted</div>
          <div style="font-size:16px; font-weight:700;">{{ $attempt->submitted_at->format('M d, Y h:i A') }}</div>
        </div>
      </div>
      <hr style="border-top: 1px solid rgba(106,77,247,0.2); margin:16px 0;">
      <div>
        <div class="score-title">TOTAL SCORE</div>
        <div class="score-value">{{ $attempt->score }}<span style="font-size:20px;">/{{ $attempt->quiz->questions->sum('points') }}</span></div>
      </div>
    </div>

    <!-- Teacher Remark -->
    @if ($attempt->teacher_remark)
      <div class="remark-card">
        <div class="remark-label">📝 Teacher's Remark</div>
        <div class="remark-text">{{ $attempt->teacher_remark }}</div>
      </div>
    @else
      <div class="remark-card" style="background:rgba(152,160,179,0.05); border-color:rgba(152,160,179,0.2);">
        <div style="color:var(--muted); font-size:13px;">No remarks have been added by the teacher yet.</div>
      </div>
    @endif

    <!-- Detailed Answers -->
    <div style="margin-top:28px;">
      <h3 style="font-weight:700; margin-bottom:16px;">Detailed Answers</h3>

      @foreach ($attempt->answers as $index => $studentAnswer)
        @php
            $isCorrect = $studentAnswer->is_correct;
            $question = $studentAnswer->question;
            $selectedOptionIds = $studentAnswer->options->pluck('id')->toArray();
        @endphp

        <div class="question-card">
          <div class="question-header">
            <div>
              <span style="font-size:15px; font-weight:700;">Question {{ $index + 1 }}</span>
              <div style="font-size:13px; line-height:1.5; margin-top:8px; color:inherit;">{{ $question->question_text }}</div>
            </div>
            <span class="question-points">{{ $question->points }} Points</span>
          </div>
          
          <div class="result-badge {{ $isCorrect ? 'correct' : 'incorrect' }}">
            @if ($isCorrect)
              ✓ Correct! (+{{ $studentAnswer->score_gained ?? $question->points }} points)
            @else
              ✗ Incorrect (0 points)
            @endif
          </div>

          {{-- SHORT ANSWER --}}
          @if ($question->type === 'short_answer')
            <div style="margin-top:12px;">
              <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:6px;">Your Answer:</div>
              <div style="padding:10px 12px; background:rgba(200,200,200,0.08); border:1px solid #d1d5db; border-radius:6px; font-size:13px;">{{ $studentAnswer->submitted_text ?? 'N/A' }}</div>
            </div>
            <div style="margin-top:12px;">
              <div style="font-size:12px; color:var(--success); font-weight:600; margin-bottom:6px;">Correct Answer:</div>
              <div style="padding:10px 12px; background:rgba(42,157,143,0.08); border:1px solid rgba(42,157,143,0.3); border-radius:6px; font-size:13px; font-weight:600; color:var(--success);">{{ $question->options->where('is_correct', true)->first()->option_text ?? 'N/A' }}</div>
            </div>
          @else
            {{-- MULTIPLE CHOICE / TRUE-FALSE / CHECKBOX --}}
            <ul class="option-list">
            @foreach ($question->options as $option)
              @php
                  $isStudentChoice = in_array($option->id, $selectedOptionIds);
                  $isActualCorrect = $option->is_correct;
                  
                  if ($isActualCorrect) {
                      $optionClass = 'correct';
                  } elseif ($isStudentChoice && !$isActualCorrect) {
                      $optionClass = 'incorrect';
                  } else {
                      $optionClass = 'neutral';
                  }
              @endphp
              <li class="{{ $optionClass }}">
                @if ($isActualCorrect && $isStudentChoice)
                  ✓
                @elseif ($isActualCorrect)
                  ✓
                @elseif ($isStudentChoice)
                  ✗
                @else
                  ○
                @endif
                {{ $option->option_text }}
                @if ($isActualCorrect)
                  <span style="margin-left:auto; font-size:11px; font-weight:600; opacity:0.7;">(Correct)</span>
                @endif
              </li>
            @endforeach
            </ul>
          @endif
        </div>
      @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="actions">
      <a href="{{ route('student.quizzes.index') }}" class="btn-back">← Back to Quiz List</a>
      
      @php
          $quiz = $attempt->quiz; 
          $attemptsMade = $quiz->attempts()->where('student_id', Auth::id())->count();
      @endphp

      @if ($attemptsMade < $quiz->max_attempts && (!$quiz->due_at || $quiz->due_at->isFuture()))
        <a href="{{ route('student.quizzes.attempt.start', $quiz->id) }}" class="btn-retry">↻ Re-attempt Quiz ({{ $attemptsMade }}/{{ $quiz->max_attempts }})</a>
      @endif
    </div>
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
</script>