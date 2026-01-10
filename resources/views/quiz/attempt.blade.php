@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main Content -->
  <main class="main">
    <div class="header">
        <div>
            <div class="title">{{ $quiz->title }}</div>
            <div class="sub">Attempt {{ $attempt->attempt_number }} of {{ $quiz->max_attempts }}</div>
        </div>
        <a href="{{ route('student.quizzes.index') }}" class="btn-kembali" id="back-btn" onclick="return confirmBackAction()">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <!-- Quiz Start Screen -->
    <div id="quiz-start-screen" style="display:block;">
      <section class="panel" style="margin-bottom:20px; margin-top:10px;">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:0;">
          <div>
            <div style="font-size:15px; color:var(--muted); font-weight:600; margin-bottom:4px;">Description</div>
            <div style="font-size:14px; line-height:1.5;">{{ $quiz->description }}</div>
          </div>
          <div>
            <div style="font-size:15px; color:var(--muted); font-weight:600; margin-bottom:8px;">Quiz Details</div>
            <div style="font-size:14px; display:flex; flex-direction:column; gap:6px;">
              <div><strong>Pencipta:</strong> {{ $quiz->creator->name ?? 'N/A' }}</div>
              <div><strong>ID:</strong> {{ $quiz->unique_code ?? 'N/A' }}</div>
              <div><strong>Tarikh tutup:</strong> @if ($quiz->due_at) {{ $quiz->due_at->format('M d, Y h:i A') }} @else N/A @endif</div>
            </div>
          </div>
        </div>
      </section>
      <div style="text-align:center; margin-top:30px; margin-bottom:20px;">
        <button type="button" onclick="startQuiz()" class="btn-start-quiz" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:16px 40px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:14px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'">
          <i class="bi bi-play-fill"></i>Mulai Kuiz
        </button>
      </div>
    </div>

    <!-- Questions Section -->
    <div id="quiz-questions-wrapper" style="display:none;">
      <input type="hidden" id="_token" value="{{ csrf_token() }}">

      @if ($quiz->questions->isEmpty())
        <section class="panel" style="margin-bottom:20px;">
          <div style="text-align:center; padding:24px; color:var(--muted);">
            <div style="font-size:15px;">This quiz has no questions available to attempt.</div>
          </div>
        </section>
      @else
        <!-- Question Navigation Counter -->
        <div style="text-align:center; margin-bottom:20px; font-size:14px; color:var(--muted); font-weight:600;">
          Question <span id="current-question-num">1</span> of {{ $quiz->questions->count() }}
        </div>

        <!-- Questions Container with Side Arrows -->
        <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px; justify-content:center;">
          <!-- Previous Arrow Button -->
          <button type="button" onclick="previousQuestion()" id="prev-arrow-btn" style="display:inline-flex !important; align-items:center !important; justify-content:center !important; width:50px !important; height:50px !important; background:transparent !important; color:#6a4df7 !important; border:none !important; border-radius:8px !important; font-weight:600 !important; font-size:28px !important; cursor:pointer !important; transition:all 0.2s ease !important; flex-shrink:0; box-shadow:0 4px 12px rgba(106,77,247,0.3) !important;" onmouseover="this.style.transform='scale(1.15)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)'">
            <i class="bi bi-chevron-left"></i>
          </button>

          <!-- Questions Content -->
          <div style="flex:1; max-width:700px; text-align:center;">
            @foreach ($quiz->questions as $key => $question)
              <section class="panel question-section" style="margin-bottom:0; display:{{ $key === 0 ? 'block' : 'none' }};" data-question-index="{{ $key }}">
            <div class="question-card" data-question-id="{{ $question->id }}">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-weight:600;">Question {{ $key + 1 }}</span>
                <span style="background:rgba(106,77,247,0.1); padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;">{{ $question->points }} Points</span>
              </div>
              <div style="font-weight:700; margin-bottom:12px; font-size:18px;">{{ $question->question_text }}</div>

              {{-- Display Options based on Question Type --}}
              @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                @foreach ($question->options as $option)
                  <div style="margin-bottom:8px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:15px;">
                      <input class="quiz-answer-input" type="radio" 
                             name="answers[{{ $question->id }}]" 
                             value="{{ $option->id }}" 
                             data-question-id="{{ $question->id }}">
                      <span>{{ $option->option_text }}</span>
                    </label>
                  </div>
                @endforeach
              
              @elseif ($question->type === 'checkbox')
                @foreach ($question->options as $option)
                  <div style="margin-bottom:8px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:15px;">
                      <input class="quiz-answer-input" type="checkbox" 
                             name="answers[{{ $question->id }}][]"
                             value="{{ $option->id }}"
                             data-question-id="{{ $question->id }}">
                      <span>{{ $option->option_text }}</span>
                    </label>
                  </div>
                @endforeach

              @elseif ($question->type === 'short_answer')
                <div style="margin-bottom:8px;">
                  <label for="q{{ $question->id }}_text" style="display:block; font-size:15px; color:var(--muted); margin-bottom:4px;">Your Answer:</label>
                  <input type="text" class="quiz-answer-input" 
                         name="answers[{{ $question->id }}][text]" 
                         id="q{{ $question->id }}_text"
                         placeholder="Type your answer here..."
                         data-question-id="{{ $question->id }}"
                         style="width:100%; padding:8px 12px; border-radius:6px; border:2px solid #d1d5db; box-sizing:border-box; font-size:15px;">
                </div>

              @elseif ($question->type === 'coding')
                <div style="margin-bottom:12px;">
                  <!-- Code Template Display -->
                  @if ($question->coding_template)
                    <div style="margin-bottom:20px;">
                      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:6px;">Template Kod:</div>
                      <div style="background:#f5f5f5; padding:12px; border-radius:8px; border:2px solid #d1d5db; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; white-space:pre; overflow-x:auto;">{{ $question->coding_template }}</div>
                    </div>
                  @endif

                  <!-- Pandangan Pelajar (Student View) - Matching edit form preview exactly -->
                  @if ($question->coding_full_code)
                    <div style="margin-top:20px;">
                      <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 8px;">Jawapan</label>
                      <div style="position: relative; background: #f5f5f5; border-radius: 8px; border: 2px solid #d1d5db; overflow: hidden; padding:0; min-height:100px; display: flex;">
                        @php
                          $hiddenLines = !empty($question->hidden_line_numbers) ? array_map('intval', explode(',', $question->hidden_line_numbers)) : [];
                          $lines = explode("\n", $question->coding_full_code);
                        @endphp
                        
                        <!-- Line Numbers Column -->
                        <div style="flex-shrink: 0; width: 40px; background: #e8e8e8; padding: 8px 0; text-align: right; font-size: 12px; font-family: 'Courier New', monospace; color: #888; border-right: 1px solid #d1d5db; line-height: 1.5; user-select: none; padding-right: 6px; display: flex; flex-direction: column;">
                          @foreach ($lines as $index => $line)
                            <div style="height: 1.5em; display: flex; align-items: center; justify-content: flex-end;">{{ $index + 1 }}</div>
                          @endforeach
                        </div>
                        
                        <!-- Code Content Column -->
                        <div style="flex: 1; padding: 8px 8px; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; color:inherit; white-space: pre; overflow-x: auto; display: flex; flex-direction: column;">
                          @foreach ($lines as $index => $line)
                            @php $lineNum = $index + 1; $isHidden = in_array($lineNum, $hiddenLines); @endphp
                            @if ($isHidden)
                              <!-- Hidden line: input field with yellow background -->
                              <div style="height: 1.5em; display: flex; align-items: center; background: rgba(255, 193, 7, 0.2); flex-shrink: 0;">
                                <input type="text" 
                                       class="quiz-answer-input coding-line-input" 
                                       data-question-id="{{ $question->id }}" 
                                       data-line-number="{{ $lineNum }}"
                                       name="answers[{{ $question->id }}][line_{{ $lineNum }}]"
                                       value=""
                                       style="flex:1; border:none; background:transparent; font-family:'Courier New', monospace; font-size:12px; padding:0 4px; outline:none; color:#000; line-height:1.5; margin:0; display:block; white-space:pre;">
                              </div>
                            @else
                              <!-- Non-hidden line: display as text (read-only) -->
                              <div style="height: 1.5em; display: flex; align-items: center; flex-shrink: 0;">{{ $line }}</div>
                            @endif
                          @endforeach
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              @endif
            </section>
            @endforeach
          </div>

          <!-- Next Arrow Button -->
          <button type="button" onclick="nextQuestion()" id="next-arrow-btn" style="display:inline-flex !important; align-items:center !important; justify-content:center !important; width:50px !important; height:50px !important; background:transparent !important; color:#6a4df7 !important; border:none !important; border-radius:8px !important; font-weight:600 !important; font-size:28px !important; cursor:pointer !important; transition:all 0.2s ease !important; flex-shrink:0; box-shadow:0 4px 12px rgba(106,77,247,0.3) !important;" onmouseover="this.style.transform='scale(1.15)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)'">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      @endif
    </div>

    <!-- Action Buttons -->
    <div id="action-buttons" style="display:none; gap:12px; justify-content:center; align-items:center; margin-top:40px; margin-bottom:20px; padding:0;">
        <!-- Submit Button -->
        <button type="button" onclick="submitQuizData()" class="btn-submit" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:14px 26px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:13px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'">
            <i class="bi bi-check-lg"></i>Hantar Kuiz
        </button>
    </div>
    <!-- <button type="button" onclick="submitQuizData()" style="flex:1; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px; transition:all .2s ease; box-shadow:0 4px 12px rgba(106,77,247,0.3);"
        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';"
        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
        Submit Quiz
    </button> -->
  </main>
</div>

{{-- JavaScript --}}
<script>
let currentQuestionIndex = 0;
const totalQuestions = {{ $quiz->questions->count() }};
let quizStarted = false;

function startQuiz() {
    quizStarted = true;
    document.getElementById('quiz-start-screen').style.display = 'none';
    document.getElementById('quiz-questions-wrapper').style.display = 'block';
    showQuestion(0);
}

// Initialize navigation buttons
function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-arrow-btn');
    const nextBtn = document.getElementById('next-arrow-btn');
    
    // Disable previous button on first question
    if (currentQuestionIndex === 0) {
        prevBtn.style.opacity = '0.5';
        prevBtn.style.cursor = 'not-allowed';
        prevBtn.disabled = true;
    } else {
        prevBtn.style.opacity = '1';
        prevBtn.style.cursor = 'pointer';
        prevBtn.disabled = false;
    }
    
    // Disable next button on last question
    if (currentQuestionIndex === totalQuestions - 1) {
        nextBtn.style.opacity = '0.5';
        nextBtn.style.cursor = 'not-allowed';
        nextBtn.disabled = true;
    } else {
        nextBtn.style.opacity = '1';
        nextBtn.style.cursor = 'pointer';
        nextBtn.disabled = false;
    }
}

function showQuestion(index) {
    if (index < 0 || index >= totalQuestions) return;
    
    // Hide all questions
    document.querySelectorAll('.question-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the selected question
    const questionSections = document.querySelectorAll('.question-section');
    if (questionSections[index]) {
        questionSections[index].style.display = 'block';
    }
    
    // Update current question number
    document.getElementById('current-question-num').textContent = index + 1;
    
    // Update button states
    updateNavigationButtons();
    
    // Show/hide submit button based on question position
    updateSubmitButtonVisibility();
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function updateSubmitButtonVisibility() {
    const actionButtons = document.getElementById('action-buttons');
    // Only show submit button on the last question
    if (currentQuestionIndex === totalQuestions - 1) {
        actionButtons.style.display = 'flex';
    } else {
        actionButtons.style.display = 'none';
    }
}

function nextQuestion() {
    if (currentQuestionIndex < totalQuestions - 1) {
        currentQuestionIndex++;
        showQuestion(currentQuestionIndex);
    }
}

function previousQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showQuestion(currentQuestionIndex);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNavigationButtons();
});

// Tab key handler for coding line inputs
document.addEventListener('keydown', function(e) {
    if (e.target.classList.contains('coding-line-input')) {
        // Handle Tab key: Insert 4 spaces
        if (e.key === 'Tab') {
            e.preventDefault();
            const input = e.target;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            
            // Insert 4 spaces
            input.value = input.value.substring(0, start) + '    ' + input.value.substring(end);
            
            // Move cursor after inserted spaces
            input.selectionStart = input.selectionEnd = start + 4;
        }
        // Handle Enter key: Go to next answerable line
        else if (e.key === 'Enter') {
            e.preventDefault();
            const input = e.target;
            const currentLineNum = parseInt(input.dataset.lineNumber);
            const questionId = input.dataset.questionId;
            
            // Find all coding line inputs for this question
            const allInputs = Array.from(document.querySelectorAll(`.coding-line-input[data-question-id="${questionId}"]`))
                .sort((a, b) => parseInt(a.dataset.lineNumber) - parseInt(b.dataset.lineNumber));
            
            // Find the next answerable (hidden) line
            let nextInput = null;
            for (let i = 0; i < allInputs.length; i++) {
                if (parseInt(allInputs[i].dataset.lineNumber) > currentLineNum) {
                    nextInput = allInputs[i];
                    break;
                }
            }
            
            // Focus on next answerable line if found
            if (nextInput) {
                nextInput.focus();
            }
        }
    }
});

function checkAllQuestionsAnswered() {
    const questions = document.querySelectorAll('[data-question-id]');
    let unansweredCount = 0;

    questions.forEach(questionCard => {
        const questionId = questionCard.getAttribute('data-question-id');
        const radios = questionCard.querySelectorAll('input[type="radio"][data-question-id]');
        const checkboxes = questionCard.querySelectorAll('input[type="checkbox"][data-question-id]');
        const textInputs = questionCard.querySelectorAll('input[type="text"]:not(.coding-line-input)[data-question-id]');
        const codingLineInputs = questionCard.querySelectorAll('input.coding-line-input[data-question-id]');

        let isAnswered = false;

        // Check if any radio button is selected
        if (radios.length > 0) {
            const checkedRadio = Array.from(radios).find(r => r.checked);
            if (checkedRadio) {
                isAnswered = true;
            }
        }

        // Check if any checkbox is selected
        if (checkboxes.length > 0 && !isAnswered) {
            const checkedCheckbox = Array.from(checkboxes).find(c => c.checked);
            if (checkedCheckbox) {
                isAnswered = true;
            }
        }

        // Check if text input has value
        if (textInputs.length > 0 && !isAnswered) {
            const filledText = Array.from(textInputs).find(t => t.value.trim() !== '');
            if (filledText) {
                isAnswered = true;
            }
        }

        // Check if any coding line input has value
        if (codingLineInputs.length > 0 && !isAnswered) {
            const filledCodeLine = Array.from(codingLineInputs).find(c => c.value.trim() !== '');
            if (filledCodeLine) {
                isAnswered = true;
            }
        }

        // If no answers found for this question type, mark as unanswered
        if (!isAnswered && (radios.length > 0 || checkboxes.length > 0 || textInputs.length > 0 || codingLineInputs.length > 0)) {
            unansweredCount++;
        }
    });

    return unansweredCount === 0;
}

function submitQuizData() {
    // Check if all questions are answered
    if (!checkAllQuestionsAnswered()) {
        alert('Sila jawab semua soalan sebelum menghantar kuiz.');
        return;
    }

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
        // Handle Short Answer Text (but not coding line inputs)
        else if (input.type === 'text' && input.value && !input.classList.contains('coding-line-input')) {
            // Short answers are sent as { 'questionId': { 'text': 'user answer' } }
            answers[qId] = { text: input.value };
        }
    });

    // Handle coding line inputs separately
    const codingLineInputs = document.querySelectorAll('.coding-line-input');
    codingLineInputs.forEach(input => {
        const qId = input.dataset.questionId;
        const lineNum = input.dataset.lineNumber;
        
        if (!answers[qId]) {
            answers[qId] = {};
        }
        
        // Store each line answer with its line number
        answers[qId][`line_${lineNum}`] = input.value;
    });

    // Prepare the final request payload
    const payload = new URLSearchParams();
    
    // Add CSRF token
    const token = document.getElementById('_token').value;
    payload.append('_token', token);
    
    // Add answers to the payload
    for (const [qId, answer] of Object.entries(answers)) {
        if (Array.isArray(answer)) {
            answer.forEach(val => payload.append(`answers[${qId}][]`, val));
        } else if (typeof answer === 'object' && answer !== null && 'text' in answer) {
            payload.append(`answers[${qId}][text]`, answer.text);
        } else if (typeof answer === 'object' && answer !== null) {
            // Handle coding line answers { line_1: value, line_2: value, ... }
            for (const [lineKey, lineValue] of Object.entries(answer)) {
                payload.append(`answers[${qId}][${lineKey}]`, lineValue);
            }
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
        redirect: 'follow', // Follow redirects
        body: payload
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        console.log('Response URL:', response.url);
        
        // If response is OK or we followed a redirect, navigate to the result page
        if (response.ok || response.status === 302 || response.status === 303 || response.status === 307) {
            // The server should redirect us, navigate to the result page
            window.location.href = response.url || window.location.href;
        } else if (response.status === 422) {
            // Validation error
            return response.json().then(data => {
                console.error('Validation errors:', data);
                alert('Validation error: ' + JSON.stringify(data.errors || data.message));
            });
        } else {
            // Handle other errors
            return response.text().then(text => {
                console.error('Error response:', text);
                alert('Error: ' + text || 'An error occurred during quiz submission.');
            });
        }
    })
    .catch(error => {
        console.error('Network error during quiz submission:', error);
        alert('A network error occurred. Please check your connection and try again.');
    });
}

// Function to confirm back action with warning
function confirmBackAction() {
    const confirmDialog = confirm('Jika anda keluar sekarang, percubaan kuiz anda TIDAK akan disimpan dan semua jawapan akan hilang.\n\nAdakah anda pasti ingin keluar?');
    return confirmDialog; // Return true to allow navigation, false to prevent it
}
</script>
@endsection