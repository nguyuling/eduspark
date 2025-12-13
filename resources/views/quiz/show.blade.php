@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">{{ $quiz->title }}</div>
                <div class="sub">Quiz Preview & Details</div>
            </div>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                ← Back to Quizzes
            </a>
        </div>

        <!-- Quiz Details Panel -->
        <section class="panel panel-spaced" style="margin-top: 60px;">
            <div class="panel-header">Quiz Setup Details</div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Unique Code</div>
                    <div style="font-size: 18px; font-weight: 700; color: var(--accent);">{{ $quiz->unique_code }}</div>
                </div>
                
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Max Attempts</div>
                    <div style="font-size: 18px; font-weight: 700;">{{ $quiz->max_attempts }}</div>
                </div>
                
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Status</div>
                    <div style="font-size: 14px;">
                        @if($quiz->is_published)
                            <span class="badge" style="background: var(--success); color: white; padding: 6px 12px; border-radius: 6px;">Published</span>
                        @else
                            <span class="badge" style="background: var(--yellow); color: #0b1220; padding: 6px 12px; border-radius: 6px;">Draft</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Due Date</div>
                    <div style="font-size: 14px;">{{ $quiz->due_at ? $quiz->due_at->format('M d, Y H:i A') : 'No Due Date' }}</div>
                </div>
                
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Total Points</div>
                    <div style="font-size: 18px; font-weight: 700;">{{ $quiz->questions->sum('points') }}</div>
                </div>
            </div>

            @if($quiz->description)
                <div style="margin-top: 20px; padding: 16px; background: rgba(106, 77, 247, 0.05); border-radius: 8px; border-left: 3px solid var(--accent);">
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 6px;">Description</div>
                    <p style="margin: 0; font-size: 14px; line-height: 1.6;">{{ $quiz->description }}</p>
                </div>
            @endif
        </section>

        <!-- Questions Section -->
        <section class="panel panel-spaced" style="margin-top: 20px;">
            <div class="panel-header">Questions ({{ $quiz->questions->count() }})</div>
            
            @forelse($quiz->questions as $index => $question)
                <div style="background: transparent; border-radius: 12px; border: 2px solid #d4c5f9; padding: 20px; margin-top: 16px; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <div style="font-size: 13px; color: var(--muted); font-weight: 600; margin-bottom: 4px;">
                                Question {{ $index + 1 }}
                            </div>
                            <div style="font-size: 18px; font-weight: 700;">{{ $question->question_text }}</div>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; flex-shrink: 0;">
                            <span class="badge" style="background: var(--accent); color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                {{ $question->points }} pts
                            </span>
                            <span class="badge" style="background: rgba(106, 77, 247, 0.1); color: var(--accent); padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Short Answer -->
                    @if ($question->type === App\Models\Question::TYPE_SHORT_ANSWER)
                        <div style="padding: 12px; background: rgba(42, 157, 143, 0.08); border-radius: 8px; border-left: 3px solid var(--success);">
                            <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 4px;">CORRECT ANSWER</div>
                            <div style="font-weight: 600;">{{ $question->correct_answer }}</div>
                        </div>

                    <!-- Multiple Choice / True-False -->
                    @elseif ($question->options->isNotEmpty())
                        <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                            @foreach($question->options->sortBy('sort_order') as $option)
                                @php
                                    $isCorrect = $option->is_correct;
                                    $bgColor = $isCorrect ? 'rgba(42, 157, 143, 0.1)' : 'rgba(212, 197, 249, 0.05)';
                                    $borderColor = $isCorrect ? 'var(--success)' : '#d4c5f9';
                                @endphp
                                <div style="padding: 12px; background: {{ $bgColor }}; border-radius: 8px; border-left: 3px solid {{ $borderColor }}; display: flex; align-items: center; gap: 12px;">
                                    @if ($isCorrect)
                                        <span style="color: var(--success); font-size: 18px; font-weight: bold;">✓</span>
                                        <span style="flex: 1;">{{ $option->option_text }}</span>
                                        <span style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Correct</span>
                                    @else
                                        <span style="color: var(--muted); font-size: 18px;">○</span>
                                        <span style="flex: 1; color: inherit;">{{ $option->option_text }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="empty-state" style="padding: 40px; text-align: center; margin-top: 20px;">
                    <div style="font-size: 48px; margin-bottom: 16px;">📝</div>
                    <div style="font-size: 16px; color: var(--muted);">This quiz has no questions yet</div>
                </div>
            @endforelse
        </section>

        <!-- Action Buttons -->
        <section class="panel panel-spaced" style="margin-top: 20px; display: flex; gap: 12px; padding: 20px; justify-content: flex-end;">
            <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-primary">
                ✏️ Edit Quiz
            </a>
            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    🗑️ Delete Quiz
                </button>
            </form>
        </section>
    </main>
</div>
@endsection