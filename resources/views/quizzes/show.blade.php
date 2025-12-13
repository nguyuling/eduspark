@extends('layouts.app') {{-- Use your main layout --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Quizzes
            </a>
            
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2>Quiz Preview: {{ $quiz->title }}</h2>
                </div>

                <div class="card-body">
                    {{-- === QUIZ HEADER DETAILS === --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h4>Quiz Setup Details</h4>
                        <p><strong>Unique Code:</strong> {{ $quiz->unique_code }}</p>
                        <p><strong>Description:</strong> {{ $quiz->description ?? 'No description provided.' }}</p>
                        <p><strong>Max Attempts:</strong> {{ $quiz->max_attempts }}</p>
                        <p><strong>Published:</strong> 
                            @if($quiz->is_published)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-warning text-dark">No (Draft)</span>
                            @endif
                        </p>
                        <p><strong>Due Date:</strong> {{ $quiz->due_at ? $quiz->due_at->format('M d, Y H:i A') : 'No Due Date' }}</p>
                        <p><strong>Total Marks:</strong> {{ $quiz->questions->sum('points') }}</p>
                    </div>

                    {{-- === QUIZ QUESTIONS LIST === --}}
                    <h4 class="mt-4 mb-3">Questions ({{ $quiz->questions->count() }})</h4>
                    
                    @forelse($quiz->questions as $index => $question)
                        <div class="card mb-3 border-dark">
                            <div class="card-header bg-light">
                                <strong>Question {{ $index + 1 }}</strong> 
                                ({{ $question->points }} Points) - 
                                <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text fw-bold">{{ $question->question_text }}</p>

                                @if ($question->type === App\Models\Question::TYPE_SHORT_ANSWER)
                                    <div class="alert alert-success mt-2">
                                        <strong>Correct Answer:</strong> {{ $question->correct_answer }}
                                    </div>
                                @elseif ($question->options->isNotEmpty())
                                    <ul class="list-group list-group-flush mt-2">
                                        @foreach($question->options->sortBy('sort_order') as $option)
                                            @php
                                                $isCorrect = $option->is_correct;
                                                $class = $isCorrect ? 'list-group-item-success' : '';
                                            @endphp
                                            <li class="list-group-item {{ $class }}">
                                                @if ($isCorrect)
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                @else
                                                    <i class="bi bi-circle me-2"></i>
                                                @endif
                                                {{ $option->option_text }}
                                                @if ($isCorrect)
                                                    <strong class="float-end">(Correct Answer)</strong>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">This quiz has no questions yet.</div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>
@endsection