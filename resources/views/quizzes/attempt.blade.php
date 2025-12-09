@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">{{ $quiz->title }}</h2>
                </div>

                <div class="card-body">
                    <p class="lead">{{ $quiz->description }}</p>
                    <p class="text-muted small mb-3">
                        Quiz Creator: <strong>
                            @if ($quiz->creator)
                                {{ $quiz->creator->name }}
                            @else
                                N/A
                            @endif
                        </strong><br>
                        Unique ID: <strong>{{ $quiz->unique_code ?? 'N/A' }}</strong>
                    </p>
                    <hr>
                    <p>
                        Attempt Number: <strong>{{ $attempt->attempt_number }}</strong> of {{ $quiz->max_attempts }} allowed.<br>
                        Deadline: @if ($quiz->due_at) {{ $quiz->due_at->format('M d, Y h:i A') }} @else N/A @endif
                    </p>
                    <hr>

                    {{-- 🚨 CRITICAL FIX: The main form wrapper is REMOVED. Inputs stand alone. --}}
                    <div id="quiz-questions-wrapper">
                        
                        {{-- Laravel CSRF token is necessary for submission --}}
                        <input type="hidden" id="_token" value="{{ csrf_token() }}">

                        @if ($quiz->questions->isEmpty())
                            <div class="alert alert-warning text-center">
                                This quiz has no questions available to attempt.
                            </div>
                        @else
                            @foreach ($quiz->questions as $key => $question)
                                <div class="card mb-4 shadow-sm" data-question-id="{{ $question->id }}">
                                    <div class="card-header bg-light">
                                        <strong>Question {{ $key + 1 }}</strong> ({{ $question->points }} Points)
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">{{ $question->question_text }}</p>

                                        {{-- Display Options based on Question Type --}}
                                        @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                                            @foreach ($question->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input quiz-answer-input" type="radio" 
                                                           name="answers[{{ $question->id }}]" 
                                                           id="q{{ $question->id }}_o{{ $option->id }}" 
                                                           value="{{ $option->id }}" 
                                                           data-question-id="{{ $question->id }}">
                                                    <label class="form-check-label" for="q{{ $question->id }}_o{{ $option->id }}">
                                                        {{ $option->option_text }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        
                                        @elseif ($question->type === 'checkbox')
                                            @foreach ($question->options as $option)
                                                <div class="form-check">
                                                    <input class="form-check-input quiz-answer-input" type="checkbox" 
                                                           name="answers[{{ $question->id }}][]"
                                                           id="q{{ $question->id }}_o{{ $option->id }}" 
                                                           value="{{ $option->id }}"
                                                           data-question-id="{{ $question->id }}">
                                                    <label class="form-check-label" for="q{{ $question->id }}_o{{ $option->id }}">
                                                        {{ $option->option_text }}
                                                    </label>
                                                </div>
                                            @endforeach

                                        @elseif ($question->type === 'short_answer')
                                            <div class="form-group">
                                                <label for="q{{ $question->id }}_text">Your Answer:</label>
                                                <input type="text" class="form-control quiz-answer-input" 
                                                       name="answers[{{ $question->id }}][text]" 
                                                       id="q{{ $question->id }}_text" 
                                                       data-question-id="{{ $question->id }}">
                                            </div>
                                        @endif
                                        
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                    </div> {{-- END quiz-questions-wrapper --}}
                    
                    <hr> 
                    
                    <div class="d-flex justify-content-between mt-4">
                        
                        {{-- QUIT QUIZ FORM (DELETE) - This must stay as a standard form --}}
                        <div class="order-1">
                            <form action="{{ route('student.quizzes.quit', $attempt->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-lg btn-danger" 
                                    onclick="return confirm('Are you sure you want to quit this attempt? All progress will be lost.')">
                                    Quit Quiz
                                </button>
                            </form>
                        </div>
                        
                        {{-- 🚨 SUBMIT QUIZ BUTTON - Now calls the explicit JavaScript function --}}
                        <div class="order-2">
                            <button type="button" class="btn btn-lg btn-success" onclick="submitQuizData()">
                                Submit Quiz
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- 🚨 JAVASCRIPT FIX: This function explicitly builds the payload and uses POST --}}
<script>
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