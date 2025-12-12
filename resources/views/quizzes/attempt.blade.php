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

input[type="text"], input[type="email"], textarea, select, input[type="file"] { width:100%; padding:11px 14px; border-radius:8px; border:1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size:14px; outline: none; transition: box-shadow .12s ease, border-color .12s ease, transform .06s ease; resize: vertical; box-sizing: border-box; }

input[type="text"]:focus, textarea:focus, select:focus, input[type="email"]:focus { box-shadow: var(--focus-glow); border-color: var(--accent); transform: translateY(-1px); }

::placeholder { color: rgba(255,255,255,0.45); }
label { font-size:13px; color:var(--muted); font-weight:600; display:block; margin-bottom:6px; }

button { cursor:pointer; padding:10px 16px; border-radius:10px; border:none; background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; font-weight:700; font-size:14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; box-shadow: 0 6px 18px rgba(8,12,32,0.25); }
button:hover { transform: translateY(-3px); opacity:0.98; }
button:active { transform: translateY(-1px); }
button.danger { background: var(--danger); }

.question-card { border-radius:var(--card-radius); padding:18px; margin-bottom:16px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); }
body.light .question-card { background: rgba(255,255,255,0.96); }
body.dark .question-card { background:#0f1724; }

.question-header { font-weight:700; font-size:15px; margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.question-points { background:rgba(106,77,247,0.1); padding:4px 10px; border-radius:6px; font-size:12px; color:var(--accent); font-weight:600; }
.question-text { font-size:14px; margin-bottom:16px; line-height:1.5; }

.form-check { display:flex; align-items:flex-start; gap:10px; margin-bottom:12px; }
.form-check input[type="radio"],
.form-check input[type="checkbox"] { 
  width:18px; height:18px; min-width:18px; cursor:pointer; 
  accent-color:var(--accent);
  border: 2px solid #d1d5db;
  border-radius: 4px;
  transition: border-color .2s ease;
  margin-top: 2px;
}
.form-check input[type="radio"] { border-radius: 50%; }
.form-check input[type="radio"]:hover,
.form-check input[type="checkbox"]:hover { border-color: #9ca3af; }
.form-check input[type="radio"]:focus,
.form-check input[type="checkbox"]:focus { outline:none; border-color:var(--accent); box-shadow: 0 0 0 3px rgba(106,77,247,0.15); }

.form-check label { margin-bottom:0; font-size:14px; color:inherit; font-weight:500; }

.form-group { margin-bottom:14px; }

.actions { display:grid; grid-template-columns:1fr 1fr 1fr; grid-template-rows:auto auto; margin-top:28px; margin-bottom:40px; gap:12px; align-items:center; justify-items:center; }
.actions form { grid-column:2; grid-row:2; background:none; border:none; padding:0; margin:0; margin-top:8px; }
.actions form button { background:none !important; border:none !important; color:var(--danger); font-size:14px; cursor:pointer; padding:0 !important; font-weight:600; text-decoration:underline; transition:opacity .2s ease; box-shadow:none !important; border-radius:0 !important; }
.actions form button:hover { opacity:0.7; }
.actions button:last-of-type { grid-column:2; grid-row:1; padding:14px 28px; font-size:15px; }
.btn-submit { background: linear-gradient(90deg,var(--success),#3bc48d); }
.btn-quit { background: var(--danger); }

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
        <div style="font-weight:700;font-size:24px;">{{ $quiz->title }}</div>
        <div style="color:var(--muted);font-size:13px;margin-top:4px;">Attempt {{ $attempt->attempt_number }} of {{ $quiz->max_attempts }}</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;font-size:24px;">🌙</button>
    </div>

    <!-- Quiz Header Info -->
    <div class="panel">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
        <div>
          <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">Description</div>
          <div style="font-size:14px; line-height:1.5;">{{ $quiz->description }}</div>
        </div>
        <div>
          <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:8px;">Quiz Details</div>
          <div style="font-size:13px; display:flex; flex-direction:column; gap:6px;">
            <div><strong>Creator:</strong> {{ $quiz->creator->name ?? 'N/A' }}</div>
            <div><strong>ID:</strong> {{ $quiz->unique_code ?? 'N/A' }}</div>
            <div><strong>Deadline:</strong> @if ($quiz->due_at) {{ $quiz->due_at->format('M d, Y h:i A') }} @else N/A @endif</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Questions Section -->
    <div id="quiz-questions-wrapper">
                        
                        {{-- Laravel CSRF token is necessary for submission --}}
                        <input type="hidden" id="_token" value="{{ csrf_token() }}">

                        @if ($quiz->questions->isEmpty())
                            <div class="panel" style="text-align:center;">
                                <div style="color:var(--muted); font-size:14px;">This quiz has no questions available to attempt.</div>
                            </div>
                        @else
                            @foreach ($quiz->questions as $key => $question)
                                <div class="question-card" data-question-id="{{ $question->id }}">
                                    <div class="question-header">
                                        <span>Question {{ $key + 1 }}</span>
                                        <span class="question-points">{{ $question->points }} Points</span>
                                    </div>
                                    <div class="question-text">{{ $question->question_text }}</div>

                                    {{-- Display Options based on Question Type --}}
                                    @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                                        @foreach ($question->options as $option)
                                            <div class="form-check">
                                                <input class="quiz-answer-input" type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       id="q{{ $question->id }}_o{{ $option->id }}" 
                                                       value="{{ $option->id }}" 
                                                       data-question-id="{{ $question->id }}">
                                                <label for="q{{ $question->id }}_o{{ $option->id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach
                                    
                                    @elseif ($question->type === 'checkbox')
                                        @foreach ($question->options as $option)
                                            <div class="form-check">
                                                <input class="quiz-answer-input" type="checkbox" 
                                                       name="answers[{{ $question->id }}][]"
                                                       id="q{{ $question->id }}_o{{ $option->id }}" 
                                                       value="{{ $option->id }}"
                                                       data-question-id="{{ $question->id }}">
                                                <label for="q{{ $question->id }}_o{{ $option->id }}">
                                                    {{ $option->option_text }}
                                                </label>
                                            </div>
                                        @endforeach

                                    @elseif ($question->type === 'short_answer')
                                        <div class="form-group">
                                            <label for="q{{ $question->id }}_text">Your Answer:</label>
                                            <input type="text" class="quiz-answer-input" 
                                                   name="answers[{{ $question->id }}][text]" 
                                                   id="q{{ $question->id }}_text"
                                                   placeholder="Type your answer here..."
                                                   data-question-id="{{ $question->id }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div> {{-- END quiz-questions-wrapper --}}
                    <div class="actions">
                        {{-- QUIT QUIZ FORM (DELETE) --}}
                        <form action="{{ route('student.quizzes.attempt.quit', $attempt->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-quit" 
                                onclick="return confirm('Are you sure you want to quit this attempt? All progress will be lost.')">
                                Quit Quiz
                            </button>
                        </form>
                        
                        {{-- SUBMIT QUIZ BUTTON --}}
                        <button type="button" class="btn-submit" onclick="submitQuizData()">
                            Submit Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
const body=document.body, toggle=document.getElementById('themeToggle');
function applyTheme(mode){
  if(mode==='light'){body.classList.replace('dark','light');toggle.textContent='☀️';}
  else{body.classList.replace('light','dark');toggle.textContent='🌙';}
}
const saved=localStorage.getItem('theme')||'dark'; applyTheme(saved);
toggle.addEventListener('click',()=>{const next=body.classList.contains('dark')?'light':'dark'; applyTheme(next); localStorage.setItem('theme',next);});

function submitQuizData() {
    // Build the answers payload manually
    const answers = {};
    
    // Get all answer inputs
    const inputs = document.querySelectorAll('.quiz-answer-input');

    inputs.forEach(input => {
        const qId = input.dataset.questionId;
        
        // Handle Checkboxes (multiple answers per question)
        if (input.type === 'checkbox' && input.checked) {
            if (!answers[qId]) {
                answers[qId] = [];
            }
            answers[qId].push(input.value);
        } 
        // Handle Radio Buttons (single answer per question)
        else if (input.type === 'radio' && input.checked) {
            answers[qId] = input.value;
        }
        // Handle Short Answer Text
        else if (input.type === 'text' && input.value) {
            // Short answers are sent as { 'questionId': { 'text': 'user answer' } }
            answers[qId] = { text: input.value };
        }
    });

    // Prepare the final request payload
    const payload = new URLSearchParams();
    
    // Add CSRF token
    const token = document.getElementById('_token').value;
    payload.append('_token', token);
    
    // Add answers to the payload
    // Note: URLSearchParams correctly handles nested arrays/objects for PHP
    for (const [qId, answer] of Object.entries(answers)) {
        if (Array.isArray(answer)) {
            answer.forEach(val => payload.append(`answers[${qId}][]`, val));
        } else if (typeof answer === 'object' && answer !== null && 'text' in answer) {
            payload.append(`answers[${qId}][text]`, answer.text);
        } else {
            payload.append(`answers[${qId}]`, answer);
        }
    }
    
    // Use Fetch API to send a guaranteed POST request
    fetch('{{ route('student.quizzes.submit', $quiz->id) }}', {
        method: 'POST', // Explicitly set the method to POST
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest' // Treat as AJAX request
        },
        body: payload
    })
    .then(response => {
        // Check if submission was successful (e.g., redirect or 200/201 status)
        if (response.ok || response.redirected) {
            // If the server redirects (typical Laravel response), follow it
            window.location.href = response.url;
        } else {
            // Handle non-successful submission (e.g., validation error, 405)
            console.error('Quiz submission failed:', response.status, response.statusText);
            alert('An error occurred during quiz submission. Please check the console for details.');
        }
    })
    .catch(error => {
        console.error('Network error during quiz submission:', error);
        alert('A network error occurred. Please check your connection and try again.');
    });
}
</script>
@endsection