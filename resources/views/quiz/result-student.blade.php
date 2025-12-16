@extends('layouts.app')

@section('content')

<!-- Main -->
<main class="main">
    <div class="header">
      <div>
        <div>
          <div class="title">{{ $attempt->quiz->title }}</div>
          <div class="sub">Keputusan percubaan kuiz anda</div>
        </div>
      </div>
        <a href="{{ route('student.quizzes.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
        </a>
    </div>

    <!-- Score Summary Card -->
    <section class="panel" style="background:linear-gradient(135deg, rgba(106,77,247,0.1), rgba(156,123,255,0.05)); border:2px solid rgba(106,77,247,0.2); padding:28px;">
      <div style="text-align:center;">
        <div style="font-size:14px; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:1px;">Jumlah Skor Anda</div>
        <div style="font-size:48px; font-weight:700; color:var(--accent); margin-bottom:8px;">
          {{ $attempt->score ?? 0 }}<span style="font-size:32px;">/ {{ $attempt->quiz->questions->sum('points') ?? 0 }}</span>
        </div>
        <div style="font-size:13px; color:var(--muted);">
          Percubaan #{{ $attempt->attempt_number }} - {{ $attempt->submitted_at->format('d M Y, H:i') }}
        </div>
      </div>
    </section>

    <!-- Teacher's Remark (if exists) -->
    @if($attempt->teacher_remark)
      <section class="panel" style="border-left:4px solid var(--accent);">
        <h3 style="margin:0 0 12px 0; font-size:16px; font-weight:700;">Ulasan Guru</h3>
        <div style="color:#333; line-height:1.6;">
          {{ $attempt->teacher_remark }}
        </div>
      </section>
    @endif

    <!-- Answers Review Section -->
    <section class="panel">
      <h3 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Ulasan Jawapan</h3>
      
      @forelse($attempt->answers as $index => $answer)
        <div style="margin-bottom:24px; padding-bottom:20px; border-bottom:1px solid #e5e7eb;">
          <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
            <div>
              <div style="font-size:14px; font-weight:700; color:var(--muted);">Soalan {{ $index + 1 }}</div>
              <div style="font-size:16px; font-weight:700; color:#0b1220; margin-top:4px;">{{ $answer->question->question_text }}</div>
            </div>
            <div style="text-align:right;">
              @if($answer->is_correct)
                <div style="background:rgba(42,157,143,0.15); color:#2A9D8F; padding:6px 12px; border-radius:6px; font-weight:700; font-size:13px;">
                  ✓ Betul
                </div>
              @else
                <div style="background:rgba(230,57,70,0.15); color:#E63946; padding:6px 12px; border-radius:6px; font-weight:700; font-size:13px;">
                  ✗ Salah
                </div>
              @endif
            </div>
          </div>

          <!-- Your Answer -->
          <div style="margin:12px 0; padding:12px; background:rgba(0,0,0,0.02); border-radius:8px;">
            <div style="font-size:12px; color:var(--muted); margin-bottom:4px; font-weight:600;">Jawapan Anda:</div>
            
            @if($answer->question->type === 'short_answer')
              <div style="font-size:14px; color:#0b1220;">{{ $answer->submitted_text ?: '(Tidak dijawab)' }}</div>
            @elseif($answer->question->type === 'multiple_choice' || $answer->question->type === 'true_false')
              @php
                $selectedOption = $answer->options?->first();
              @endphp
              <div style="font-size:14px; color:#0b1220;">
                {{ $selectedOption?->option_text ?? '(Tidak dijawab)' }}
              </div>
            @elseif($answer->question->type === 'checkbox')
              @php
                $selectedOptions = $answer->options ?? collect();
              @endphp
              @if($selectedOptions->count() > 0)
                <ul style="margin:0; padding-left:20px;">
                  @foreach($selectedOptions as $option)
                    <li style="font-size:14px; color:#0b1220; margin-bottom:4px;">{{ $option->option_text }}</li>
                  @endforeach
                </ul>
              @else
                <div style="font-size:14px; color:#0b1220;">(Tidak dijawab)</div>
              @endif
            @endif
          </div>

          <!-- Correct Answer (if wrong) -->
          @if(!$answer->is_correct)
            <div style="margin:12px 0; padding:12px; background:rgba(42,157,143,0.08); border-radius:8px; border-left:3px solid #2A9D8F;">
              <div style="font-size:12px; color:var(--muted); margin-bottom:4px; font-weight:600;">Jawapan yang Betul:</div>
              
              @php
                $correctOptions = $answer->question->options->where('is_correct', true);
              @endphp
              
              @if($answer->question->type === 'short_answer')
                <div style="font-size:14px; color:#2A9D8F; font-weight:600;">
                  {{ $correctOptions->first()?->option_text ?? 'N/A' }}
                </div>
              @else
                @if($correctOptions->count() > 0)
                  <ul style="margin:0; padding-left:20px;">
                    @foreach($correctOptions as $option)
                      <li style="font-size:14px; color:#2A9D8F; font-weight:600; margin-bottom:4px;">{{ $option->option_text }}</li>
                    @endforeach
                  </ul>
                @endif
              @endif
            </div>
          @endif

          <!-- Points -->
          <div style="margin-top:12px; text-align:right;">
            <span style="font-size:13px; color:var(--muted);">
              Markah: 
              <strong style="color:var(--accent);">{{ $answer->score_gained ?? 0 }} / {{ $answer->question->points }}</strong>
            </span>
          </div>
        </div>
      @empty
        <div style="text-align:center; padding:40px; color:var(--muted);">
          <p style="font-size:16px;">Tiada jawapan ditemui.</p>
        </div>
      @endforelse
    </section>
</main>

@endsection
