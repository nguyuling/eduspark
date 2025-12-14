@extends('layouts.app')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center; margin-bottom:24px; margin-left:40px; margin-right:40px; margin-top:20px;">
  <div>
    <div style="font-weight:700;font-size:24px;">{{ $attempt->quiz->title }}</div>
    <div style="color:var(--muted);font-size:13px;margin-top:4px;">Quiz Results</div>
  </div>
</div>

<!-- Score Summary Card -->
<section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
  <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:0;">
    <div>
      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">Student</div>
      <div style="font-size:16px; font-weight:700;">{{ $attempt->student->name }}</div>
    </div>
    <div>
      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">Attempt</div>
      <div style="font-size:16px; font-weight:700;">{{ $attempt->attempt_number }} of {{ $attempt->quiz->max_attempts }}</div>
    </div>
    <div>
      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:4px;">Submitted</div>
      <div style="font-size:16px; font-weight:700;">{{ $attempt->submitted_at->format('M d, Y h:i A') }}</div>
    </div>
  </div>
  <hr style="border-top: 1px solid rgba(106,77,247,0.2); margin:16px 0;">
  <div>
    <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:8px; text-transform:uppercase;">Total Score</div>
    <div style="font-size:32px; font-weight:700; color:var(--accent);">{{ $attempt->score }}<span style="font-size:18px; font-weight:600;">/{{ $attempt->quiz->questions->sum('points') }}</span></div>
  </div>
</section>

<!-- Teacher Remark -->
<section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
  @if ($attempt->teacher_remark)
    <div>
      <div style="font-size:12px; color:var(--muted); font-weight:600; margin-bottom:8px;">Cadangan daripada guru</div>
      <div style="font-size:14px; line-height:1.6;">{{ $attempt->teacher_remark }}</div>
    </div>
  @else
    <div style="color:var(--muted); font-size:13px;">Tiada cadangan daripada guru setakat ini.</div>
  @endif
</section>

<!-- Detailed Answers -->
<section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
  <h3 style="font-weight:700; margin-bottom:20px; font-size:16px;">Jawapan Terperinci</h3>

  @foreach ($attempt->answers as $index => $studentAnswer)
    @php
        $isCorrect = $studentAnswer->is_correct;
        $question = $studentAnswer->question;
        $selectedOptionIds = $studentAnswer->options->pluck('id')->toArray();
    @endphp

    <div style="margin-bottom:20px; padding:16px; background:rgba(106,77,247,0.03); border-radius:8px; border-left:4px solid {{ $isCorrect ? 'var(--success)' : 'var(--danger)' }};">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
        <div>
          <span style="font-size:13px; font-weight:700;">Soalan {{ $index + 1 }}</span>
          <div style="font-size:14px; line-height:1.5; margin-top:8px; font-weight:700;">{{ $question->question_text }}</div>
        </div>
        <span style="background:{{ $isCorrect ? 'rgba(42,157,143,0.1)' : 'rgba(230,57,70,0.1)' }}; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:{{ $isCorrect ? 'var(--success)' : 'var(--danger)' }}; white-space:nowrap;">
          {{ $question->points }} Points
        </span>
      </div>

      <div style="background:{{ $isCorrect ? 'rgba(42,157,143,0.1)' : 'rgba(230,57,70,0.1)' }}; padding:8px 12px; border-radius:6px; font-size:12px; font-weight:600; color:{{ $isCorrect ? 'var(--success)' : 'var(--danger)' }}; margin-bottom:12px;">
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
        <div style="display:flex; flex-direction:column; gap:8px; margin-top:12px;">
        @foreach ($question->options as $option)
          @php
              $isStudentChoice = in_array($option->id, $selectedOptionIds);
              $isActualCorrect = $option->is_correct;
              
              if ($isActualCorrect) {
                  $bgColor = 'rgba(42,157,143,0.08)';
                  $borderColor = 'rgba(42,157,143,0.3)';
                  $textColor = 'var(--success)';
                  $icon = '✓';
              } elseif ($isStudentChoice && !$isActualCorrect) {
                  $bgColor = 'rgba(230,57,70,0.08)';
                  $borderColor = 'rgba(230,57,70,0.3)';
                  $textColor = 'var(--danger)';
                  $icon = '✗';
              } else {
                  $bgColor = 'transparent';
                  $borderColor = '#d1d5db';
                  $textColor = 'inherit';
                  $icon = '○';
              }
          @endphp
          <div style="display:flex; align-items:center; padding:10px 12px; background:{{ $bgColor }}; border:1px solid {{ $borderColor }}; border-radius:6px; font-size:13px; color:{{ $textColor }};">
            <span style="margin-right:8px; font-weight:700;">{{ $icon }}</span>
            <span>{{ $option->option_text }}</span>
            @if ($isActualCorrect)
              <span style="margin-left:auto; font-size:11px; font-weight:600; opacity:0.7;">(Correct)</span>
            @endif
          </div>
        @endforeach
        </div>
      @endif
    </div>
  @endforeach
</section>

<!-- Action Buttons -->
<section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:40px;">
  <div style="display:flex; gap:12px;">
    <a href="{{ route('student.quizzes.index') }}" style="flex:1; display:flex; align-items:center; justify-content:center; padding:12px 24px; background:transparent; border:2px solid var(--accent); color:var(--accent); border-radius:8px; font-weight:700; cursor:pointer; font-size:14px; transition:all .2s ease; text-decoration:none;" 
        onmouseover="this.style.background='rgba(106,77,247,0.08)';" 
        onmouseout="this.style.background='transparent';">
      ← Back to Quiz List
    </a>
    
    @php
        $quiz = $attempt->quiz; 
        $attemptsMade = $quiz->attempts()->where('student_id', Auth::id())->count();
    @endphp

    @if ($attemptsMade < $quiz->max_attempts && (!$quiz->due_at || $quiz->due_at->isFuture()))
      <a href="{{ route('student.quizzes.start', $quiz->id) }}" style="flex:1; display:flex; align-items:center; justify-content:center; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:14px; transition:all .2s ease; box-shadow:0 4px 12px rgba(106,77,247,0.3); text-decoration:none;"
          onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';"
          onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
        ↻ Re-attempt Quiz ({{ $attemptsMade }}/{{ $quiz->max_attempts }})
      </a>
    @endif
  </div>
</section>

<div style="height:60px;"></div>

@endsection