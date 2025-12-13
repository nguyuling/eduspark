@extends('layouts.app')

@section('content')

<div class="app">
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