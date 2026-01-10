@extends('layouts.app')

@section('content')

<style>
  /* Option div styling */
  .option-div {
    border: 2px solid rgba(106,77,247,0.3);
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: rgba(106,77,247,0.02);
  }
  
  .option-div:hover {
    border-color: rgba(106,77,247,0.6);
    background: rgba(106,77,247,0.08);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(106,77,247,0.15);
  }
  
  .option-div.selected {
    border-color: rgba(106,77,247,0.8);
    background: rgba(106,77,247,0.15);
    box-shadow: 0 4px 12px rgba(106,77,247,0.25);
  }
</style>

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
      <div style="display:flex; justify-content:center;">
        <div style="max-width:700px; width:100%;">
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
            <button type="button" onclick="startQuiz()" class="btn-start-quiz" id="start-quiz-btn" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:16px 40px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:14px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'">
              <i class="bi bi-arrow-right"></i><span id="start-quiz-text">Mulai Kuiz</span>
            </button>
          </div>
        </div>
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
        <div style="text-align:center; margin-bottom:20px; font-size:14px; font-weight:600;">
          Question <span id="current-question-num">1</span> of {{ $quiz->questions->count() }}
        </div>

        <!-- Questions Container with Side Arrows -->
        <div style="display:flex; align-items:center; gap:40px; margin-bottom:20px; justify-content:center; margin-top:40px;">
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
                    <span style="font-weight:600; color:#6a4df7;">Question {{ $key + 1 }}</span>
                    <span style="background:rgba(106,77,247,0.1); padding:4px 8px; border-radius:4px; font-size:12px; font-weight:600;">{{ $question->points }} Points</span>
                  </div>
                  <div style="font-weight:700; margin-bottom:20px; font-size:18px;">{{ $question->question_text }}</div>
                </div>

                {{-- Display Options based on Question Type --}}
                @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                  <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; text-align:left;">
                    @foreach ($question->options as $option)
                      <div class="option-div" data-option-id="{{ $option->id }}" onclick="updateOptionSelection(this, {{ $question->id }})">
                        <label style="display:flex; align-items:center; gap:12px; cursor:pointer; font-size:15px; margin:0;">
                          <input class="quiz-answer-input" type="radio" 
                                 name="answers[{{ $question->id }}]" 
                                 value="{{ $option->id }}" 
                                 data-question-id="{{ $question->id }}"
                                 style="width:20px; height:20px; cursor:pointer; accent-color:#6a4df7; flex-shrink:0;">
                          <span style="flex:1;">{{ $option->option_text }}</span>
                        </label>
                      </div>
                    @endforeach
                  </div>
                
                @elseif ($question->type === 'checkbox')
                  <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:12px; text-align:left;">
                    @foreach ($question->options as $option)
                      <div class="option-div" data-option-id="{{ $option->id }}" onclick="updateCheckboxSelection(this)">
                        <label style="display:flex; align-items:center; gap:12px; cursor:pointer; font-size:15px; margin:0;">
                          <input class="quiz-answer-input" type="checkbox" 
                                 name="answers[{{ $question->id }}][]"
                                 value="{{ $option->id }}"
                                 data-question-id="{{ $question->id }}"
                                 style="width:20px; height:20px; cursor:pointer; accent-color:#6a4df7; flex-shrink:0;">
                          <span style="flex:1;">{{ $option->option_text }}</span>
                        </label>
                      </div>
                    @endforeach
                  </div>

                @elseif ($question->type === 'short_answer')
                  <div style="text-align:left;">
                    <label for="q{{ $question->id }}_text" style="display:block; font-size:15px; color:var(--muted); margin-bottom:8px; font-weight:600;">Your Answer:</label>
                    <input type="text" class="quiz-answer-input" 
                           name="answers[{{ $question->id }}][text]" 
                           id="q{{ $question->id }}_text"
                           placeholder="Type your answer here..."
                           data-question-id="{{ $question->id }}"
                           style="width:100%; padding:12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:15px;">
                  </div>

                @elseif ($question->type === 'coding')
                  <!-- Code Template Display -->
                  @if ($question->coding_template)
                    <div style="margin-bottom:20px; border:2px solid rgba(106,77,247,0.3); border-radius:12px; padding:16px; background:rgba(106,77,247,0.02); text-align:left;">
                      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:8px;">Template Kod:</div>
                      <div style="background:#f5f5f5; padding:12px; border-radius:8px; border:2px solid #d1d5db; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; white-space:pre; overflow-x:auto;">{{ $question->coding_template }}</div>
                    </div>
                  @endif

                  <!-- Pandangan Pelajar (Student View) -->
                  @if ($question->coding_full_code)
                    <div style="border:2px solid rgba(106,77,247,0.3); border-radius:12px; padding:16px; background:rgba(106,77,247,0.02); margin-bottom:12px; text-align:left;">
                      <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 12px;">Jawapan</label>
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

    <!-- Progress Bar -->
    <div id="progress-bar-wrapper" style="display:none; margin-bottom:20px; width:100%; justify-content:center; padding:0 20px; box-sizing:border-box;">
      <div style="display:flex; align-items:center; gap:40px; justify-content:center; width:100%;">
        <!-- Spacer for left arrow (50px + 40px gap) -->
        <div style="width:90px; flex-shrink:0;"></div>
        
        <!-- Progress bar content -->
        <div style="flex:1; max-width:700px;">
          <div style="background:linear-gradient(90deg, rgba(168, 85, 247, 0.1), rgba(147, 51, 234, 0.1)); height:28px; border-radius:14px; overflow:hidden; box-shadow:0 4px 15px rgba(168, 85, 247, 0.25), inset 0 1px 2px rgba(255, 255, 255, 0.3); position:relative; border:1px solid rgba(168, 85, 247, 0.2); width:100%;">
            <div id="progress-bar" style="background:linear-gradient(90deg, #A855F7, #9333EA); height:100%; border-radius:14px; width:1%; transition:width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); display:flex; align-items:center; justify-content:flex-end; box-shadow:0 0 20px rgba(168, 85, 247, 0.4), inset 0 1px 2px rgba(255, 255, 255, 0.2); position:relative; padding-right:8px;">
              <span id="progress-percentage" style="color:#fff; font-size:13px; font-weight:700; text-shadow:0 2px 4px rgba(0,0,0,0.3); position:relative; z-index:10; white-space:nowrap;">0%</span>
            </div>
          </div>
        </div>
        
        <!-- Spacer for right arrow (50px + 40px gap) -->
        <div style="width:90px; flex-shrink:0;"></div>
      </div>
    </div>
    
    <!-- Action Buttons -->
    <div id="action-buttons" style="display:none; gap:12px; justify-content:center; align-items:center; margin-bottom:20px; padding:0; margin-top:20px;">
        <!-- Submit Button -->
        <button type="button" onclick="submitQuizData()" class="btn-submit" style="display:inline-flex !important; align-items:center !important; gap:8px !important; padding:14px 26px !important; background:linear-gradient(90deg, #A855F7, #9333EA) !important; color:#fff !important; border:none !important; text-decoration:none !important; border-radius:8px !important; font-weight:600 !important; font-size:13px !important; cursor:pointer !important; transition:all 0.2s ease !important; box-shadow:0 2px 8px rgba(168, 85, 247, 0.3) !important;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(168, 85, 247, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(168, 85, 247, 0.3)'">
            <i class="bi bi-check-lg"></i>Hantar Kuiz
        </button>
    </div>
  </main>
</div>

{{-- JavaScript --}}
<script>
let currentQuestionIndex = 0;
const totalQuestions = {{ $quiz->questions->count() }};
let quizStarted = false;

// --- ADD THESE FUNCTIONS TO FIX THE ERROR ---
function updateOptionSelection(div, questionId) {
    // Optionally, you can add logic here if you want to visually select the option-div.
    // For now, just trigger the input inside to be checked (for accessibility).
    const input = div.querySelector('input[type="radio"]');
    if (input) {
        input.checked = true;
        // Trigger change event for progress bar update
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function updateCheckboxSelection(div) {
    // Toggle the checkbox inside the div
    const input = div.querySelector('input[type="checkbox"]');
    if (input) {
        input.checked = !input.checked;
        // Trigger change event for progress bar update
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function startQuiz() {
    quizStarted = true;
    document.getElementById('quiz-start-screen').style.display = 'none';
    document.getElementById('quiz-questions-wrapper').style.display = 'block';
    document.getElementById('progress-bar-wrapper').style.display = 'flex';
    showQuestion(0);
}

// Initialize navigation buttons
function updateNavigationButtons() {
    const prevBtn = document.getElementById('prev-arrow-btn');
    const nextBtn = document.getElementById('next-arrow-btn');
    
    // Enable previous button even on first question to go back to start screen
    prevBtn.style.opacity = '1';
    prevBtn.style.cursor = 'pointer';
    prevBtn.disabled = false;
    
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
    
    // Update progress bar
    updateProgressBar();
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function updateProgressBar() {
    const progressBar = document.getElementById('progress-bar');
    const progressPercentage = document.getElementById('progress-percentage');
    
    let answeredCount = 0;
    // --- FIX: Select only .question-card elements, not all [data-question-id] ---
    const questionCards = document.querySelectorAll('.question-card[data-question-id]');
    
    questionCards.forEach(questionCard => {
        const questionId = questionCard.getAttribute('data-question-id');
        const radios = questionCard.parentElement.querySelectorAll('input[type="radio"][data-question-id="' + questionId + '"]');
        const checkboxes = questionCard.parentElement.querySelectorAll('input[type="checkbox"][data-question-id="' + questionId + '"]');
        const textInputs = questionCard.parentElement.querySelectorAll('input[type="text"]:not(.coding-line-input)[data-question-id="' + questionId + '"]');
        const codingLineInputs = questionCard.parentElement.querySelectorAll('input.coding-line-input[data-question-id="' + questionId + '"]');
        
        let isAnswered = false;
        
        if (radios.length > 0) {
            const checkedRadio = Array.from(radios).find(r => r.checked);
            if (checkedRadio) isAnswered = true;
        }
        if (checkboxes.length > 0 && !isAnswered) {
            const checkedCheckbox = Array.from(checkboxes).find(c => c.checked);
            if (checkedCheckbox) isAnswered = true;
        }
        if (textInputs.length > 0 && !isAnswered) {
            const filledText = Array.from(textInputs).find(t => t.value.trim() !== '');
            if (filledText) isAnswered = true;
        }
        if (codingLineInputs.length > 0 && !isAnswered) {
            const filledCodeLine = Array.from(codingLineInputs).find(c => c.value.trim() !== '');
            if (filledCodeLine) isAnswered = true;
        }

        // Debug: log each question's answer state
        console.log('Question', questionId, 'answered:', isAnswered);

        if (isAnswered) answeredCount++;
    });

    // Debug: log total answered and total questions
    console.log('Answered count:', answeredCount, 'of', totalQuestions);

    // Calculate progress based on answered questions
    const progress = (answeredCount / totalQuestions) * 100;
    progressBar.style.width = progress + '%';
    progressPercentage.textContent = Math.round(progress) + '%';

    // Debug: log progress bar width and percentage
    console.log('Progress bar width:', progressBar.style.width, 'Percentage:', progressPercentage.textContent);

    // Update submit button visibility based on progress
    updateSubmitButtonVisibility();
}

function updateSubmitButtonVisibility() {
    const actionButtons = document.getElementById('action-buttons');
    let answeredCount = 0;
    // --- FIX: Select only .question-card elements ---
    const questionCards = document.querySelectorAll('.question-card[data-question-id]');
    
    questionCards.forEach(questionCard => {
        const questionId = questionCard.getAttribute('data-question-id');
        const radios = questionCard.parentElement.querySelectorAll('input[type="radio"][data-question-id="' + questionId + '"]');
        const checkboxes = questionCard.parentElement.querySelectorAll('input[type="checkbox"][data-question-id="' + questionId + '"]');
        const textInputs = questionCard.parentElement.querySelectorAll('input[type="text"]:not(.coding-line-input)[data-question-id="' + questionId + '"]');
        const codingLineInputs = questionCard.parentElement.querySelectorAll('input.coding-line-input[data-question-id="' + questionId + '"]');
        
        let isAnswered = false;

        if (radios.length > 0) {
            const checkedRadio = Array.from(radios).find(r => r.checked);
            if (checkedRadio) isAnswered = true;
        }
        if (checkboxes.length > 0 && !isAnswered) {
            const checkedCheckbox = Array.from(checkboxes).find(c => c.checked);
            if (checkedCheckbox) isAnswered = true;
        }
        if (textInputs.length > 0 && !isAnswered) {
            const filledText = Array.from(textInputs).find(t => t.value.trim() !== '');
            if (filledText) isAnswered = true;
        }
        if (codingLineInputs.length > 0 && !isAnswered) {
            const filledCodeLine = Array.from(codingLineInputs).find(c => c.value.trim() !== '');
            if (filledCodeLine) isAnswered = true;
        }

        if (isAnswered) answeredCount++;
    });
    
    const progress = (answeredCount / totalQuestions) * 100;
    
    // Show submit button only when progress is 100%
    if (progress === 100) {
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
    } else if (currentQuestionIndex === 0) {
        // Go back to quiz start screen
        document.getElementById('quiz-questions-wrapper').style.display = 'none';
        document.getElementById('quiz-start-screen').style.display = 'block';
        document.getElementById('action-buttons').style.display = 'none';
        document.getElementById('progress-bar-wrapper').style.display = 'none';
        
        // Update button text to "Sambung Kuiz"
        document.getElementById('start-quiz-text').textContent = 'Sambung Kuiz';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateNavigationButtons();
    // Keep progress bar hidden initially until quiz starts
    document.getElementById('progress-bar-wrapper').style.display = 'none';
    
    // Set selected class for checked inputs
    document.querySelectorAll('.option-div').forEach(optDiv => {
        const input = optDiv.querySelector('input');
        if (input && input.checked) {
            optDiv.classList.add('selected');
        }
    });
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
    const questionCards = document.querySelectorAll('.question-card[data-question-id]');
    let unansweredCount = 0;

    questionCards.forEach(questionCard => {
        const questionId = questionCard.getAttribute('data-question-id');
        const radios = questionCard.parentElement.querySelectorAll('input[type="radio"][data-question-id="' + questionId + '"]');
        const checkboxes = questionCard.parentElement.querySelectorAll('input[type="checkbox"][data-question-id="' + questionId + '"]');
        const textInputs = questionCard.parentElement.querySelectorAll('input[type="text"]:not(.coding-line-input)[data-question-id="' + questionId + '"]');
        const codingLineInputs = questionCard.parentElement.querySelectorAll('input.coding-line-input[data-question-id="' + questionId + '"]');

        let isAnswered = false;

        if (radios.length > 0) {
            const checkedRadio = Array.from(radios).find(r => r.checked);
            if (checkedRadio) isAnswered = true;
        }
        if (checkboxes.length > 0 && !isAnswered) {
            const checkedCheckbox = Array.from(checkboxes).find(c => c.checked);
            if (checkedCheckbox) isAnswered = true;
        }
        if (textInputs.length > 0 && !isAnswered) {
            const filledText = Array.from(textInputs).find(t => t.value.trim() !== '');
            if (filledText) isAnswered = true;
        }
        if (codingLineInputs.length > 0 && !isAnswered) {
            const filledCodeLine = Array.from(codingLineInputs).find(c => c.value.trim() !== '');
            if (filledCodeLine) isAnswered = true;
        }

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
            // Handle coding line answers
            for (const [lineKey, lineValue] of Object.entries(answer)) {
                payload.append(`answers[${qId}][${lineKey}]`, lineValue);
            }
        } else {
            payload.append(`answers[${qId}]`, answer);
        }
    }
    
    // Use Fetch API to send POST request
    fetch('{{ route('student.quizzes.submit', $quiz->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        redirect: 'follow',
        body: payload
    })
    .then(response => {
        if (response.ok) {
            window.location.href = response.url;
        } else if (response.status === 422) {
            return response.json().then(data => {
                alert('Validation error: ' + JSON.stringify(data.errors || data.message));
            });
        } else {
            return response.text().then(text => {
                alert('Error: ' + text || 'An error occurred.');
            });
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        alert('A network error occurred. Please check your connection and try again.');
    });
}

function confirmBackAction() {
    const confirmDialog = confirm('Jika anda keluar sekarang, percubaan kuiz anda TIDAK akan disimpan dan semua jawapan akan hilang.\n\nAdakah anda pasti ingin keluar?');
    return confirmDialog;
}

// Add event listeners to update option selection display
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('quiz-answer-input')) {
        if (e.target.type === 'radio') {
            // Update radio button visual feedback
            const questionId = e.target.dataset.questionId;
            const allOptions = document.querySelectorAll(`input[data-question-id="${questionId}"][type="radio"]`);
            allOptions.forEach(radio => {
                const optDiv = radio.closest('.option-div');
                if (optDiv) {
                    if (radio.checked) {
                        optDiv.classList.add('selected');
                    } else {
                        optDiv.classList.remove('selected');
                    }
                }
            });
        } else if (e.target.type === 'checkbox') {
            // Update checkbox visual feedback
            const optDiv = e.target.closest('.option-div');
            if (optDiv) {
                if (e.target.checked) {
                    optDiv.classList.add('selected');
                } else {
                    optDiv.classList.remove('selected');
                }
            }
        }
        updateProgressBar();
    }
});

// Listen for input events on text and coding inputs
document.addEventListener('input', function(e) {
    if (
        e.target.classList.contains('quiz-answer-input') &&
        (e.target.type === 'text' || e.target.classList.contains('coding-line-input'))
    ) {
        updateProgressBar();
    }
});
</script>
@endsection