@extends('layouts.app') {{-- Assume you have a master layout file --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">Quiz Results: {{ $attempt->quiz->title }}</h2>
                </div>

                <div class="card-body">
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Student: <strong>{{ $attempt->student->name }}</strong></h4>
                            <p>Attempt: <strong>{{ $attempt->attempt_number }}</strong></p>
                            <p>Submitted: {{ $attempt->submitted_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="alert alert-success p-3">
                                <h3 class="mb-0">Total Score:</h3>
                                {{-- Total score calculation (relies on Quiz model and eager loading) --}}
                                <h1 class="display-4"><strong>{{ $attempt->score }} / {{ $attempt->quiz->questions->sum('points') }}</strong></h1>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Teacher Comment/Remark (UC0012) --}}
                    @if ($attempt->teacher_remark)
                        <div class="alert alert-info mb-4">
                            <h5><i class="bi bi-chat-left-text-fill"></i> Teacher's Remark:</h5>
                            <p class="mb-0">{{ $attempt->teacher_remark }}</p>
                        </div>
                    @else
                        <div class="alert alert-light border">
                            <p class="mb-0">No remarks have been added by the teacher yet.</p>
                        </div>
                    @endif

                    <h4 class="mt-5 mb-3">Detailed Answers:</h4>

                    @foreach ($attempt->answers as $index => $studentAnswer)
                        @php
                            $isCorrect = $studentAnswer->is_correct;
                            $question = $studentAnswer->question;
                            $answerClass = $isCorrect ? 'border-success' : 'border-danger';
                            
                            // Prepare selected option IDs for Checkbox/MC/TF logic
                            $selectedOptionIds = $studentAnswer->options->pluck('id')->toArray();
                        @endphp

                        <div class="question-result mb-4 p-3 border-start border-5 {{ $answerClass }} bg-light">
                            <h5>
                                {{ $index + 1 }}. {{ $question->question_text }}
                                <span class="float-end badge bg-secondary">{{ $question->points }} Points</span>
                            </h5>
                            
                            <hr class="my-2">

                            @if ($isCorrect)
                                <p class="text-success"><i class="bi bi-check-circle-fill"></i> **Result:** Correct! ({{ $studentAnswer->score_gained ?? $question->points }} points gained)</p>
                            @else
                                <p class="text-danger"><i class="bi bi-x-circle-fill"></i> **Result:** Incorrect ({{ $studentAnswer->score_gained ?? 0 }} points gained)</p>
                            @endif

                            {{-- LOGIC FOR SHORT ANSWER --}}
                            @if ($question->type === 'short_answer')
                                <p class="mt-3">
                                    <strong>Your Typed Answer:</strong> 
                                    <span class="p-1 rounded bg-white border">{{ $studentAnswer->submitted_text ?? 'N/A' }}</span>
                                </p>
                                <p>
                                    <strong>Correct Answer:</strong> 
                                    <span class="text-success fw-bold">{{ $question->options->where('is_correct', true)->first()->option_text ?? 'N/A' }}</span>
                                </p>
                            @else
                                {{-- LOGIC FOR MC/TF/CHECKBOX --}}
                                <ul class="list-unstyled mt-3">
                                @foreach ($question->options as $option)
                                    @php
                                        $isStudentChoice = in_array($option->id, $selectedOptionIds);
                                        $isActualCorrect = $option->is_correct;

                                        $optionClass = '';
                                        if ($isActualCorrect) {
                                            $optionClass = 'text-success fw-bold border border-success p-1 rounded';
                                        } elseif ($isStudentChoice && !$isActualCorrect) {
                                            // Highlight the student's wrong selection
                                            $optionClass = 'text-danger fw-bold border border-danger p-1 rounded';
                                        }
                                    @endphp
                                    
                                    <li class="{{ $optionClass }}">
                                        @if ($isActualCorrect && $isStudentChoice)
                                            <i class="bi bi-check2-all me-2 text-success"></i> {{-- Correctly selected correct answer --}}
                                        @elseif ($isActualCorrect)
                                            <i class="bi bi-check-circle-fill me-2 text-success"></i> {{-- Correct answer not selected --}}
                                        @elseif ($isStudentChoice)
                                            <i class="bi bi-x-circle-fill me-2 text-danger"></i> {{-- Wrong answer selected --}}
                                        @else
                                            <i class="bi bi-circle me-2"></i>
                                        @endif
                                        {{ $option->option_text }}
                                        @if ($isActualCorrect)
                                            (Correct Answer)
                                        @endif
                                    </li>
                                @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="card-footer d-flex justify-content-between mt-4 p-3 bg-light rounded shadow-sm">
                    
                    {{-- 1. Back to Quiz List Button --}}
                    <a href="{{ route('student.quizzes.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Back to Quiz List
                    </a>
                    
                    @php
                        // Data required for the re-attempt button (relies on QuizController@showResult loading 'quiz')
                        $quiz = $attempt->quiz; 
                        $attemptsMade = $quiz->attempts()->where('student_id', Auth::id())->count();
                    @endphp

                    {{-- 2. Re-attempt Button (Only visible if attempts remain and deadline is future) --}}
                    @if ($attemptsMade < $quiz->max_attempts && (!$quiz->due_at || $quiz->due_at->isFuture()))
                        <a href="{{ route('student.quizzes.attempt.start', $quiz->id) }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-arrow-clockwise"></i> Re-attempt Quiz ({{ $attemptsMade }}/{{ $quiz->max_attempts }})
                        </a>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection