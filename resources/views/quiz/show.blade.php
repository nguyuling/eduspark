@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Lihat Kuiz: {{ $quiz->title }}</div>
        <div class="sub">Papar soalan dan tetapan kuiz</div>
      </div>
        <a href="{{ route('teacher.quizzes.index') }}" class="btn-kembali">
            <i class="bi bi-arrow-left"></i>Kembali
        </a>
    </div>

    <!-- Quiz Format Section (Read-Only) -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <h2 style="margin:0 0 20px 0; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Format Kuiz</h2>

      <!-- Title -->
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color:var(--muted);">Tajuk Kuiz</label>
        <div style="font-size: 18px; font-weight: 700;">{{ $quiz->title }}</div>
      </div>

      <!-- Description -->
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color:var(--muted);">Penerangan</label>
        <div style="font-size: 14px; line-height:1.6; white-space: pre-wrap; word-wrap: break-word;">{{ $quiz->description ?? '(Tiada penerangan)' }}</div>
      </div>

      <!-- Bottom Row: Due Date, Max Attempts, Publish Status -->
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
        <!-- Due Date -->
        <div>
          <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color:var(--muted);">Tarikh Akhir</label>
          <div style="font-size: 16px; font-weight: 600;">{{ $quiz->due_at ? $quiz->due_at->format('M d, Y H:i') : '(Tiada)' }}</div>
        </div>

        <!-- Max Attempts -->
        <div>
          <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color:var(--muted);">Percubaan Maksimum</label>
          <div style="font-size: 16px; font-weight: 600;">{{ $quiz->max_attempts }}</div>
        </div>

        <!-- Publish Status -->
        <div>
          <label style="display: block; font-weight: 600; font-size: 14px; margin-bottom: 6px; color:var(--muted);">Status</label>
          <div style="font-size: 16px; font-weight: 600; color: {{ $quiz->is_published ? 'var(--success)' : 'var(--danger)' }};">
            @if($quiz->is_published)
              ✓ Diterbitkan
            @else
              ✗ Draf
            @endif
          </div>
        </div>
      </div>
    </section>

    <!-- Questions Header -->
    <section style="margin-top:40px; margin-bottom:20px;">
      <h2 style="margin:0; font-size:18px; font-weight:700;">Soalan ({{ $quiz->questions->count() }})</h2>
    </section>

    <!-- Questions Container - Each question appears as its own section -->
    <div id="questions-container">
      @forelse($quiz->questions as $index => $question)
        <section class="panel" style="margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #d4c5f9;">
            <h3 style="margin:0; font-size:16px; font-weight:700;">Soalan {{ $index + 1 }}</h3>
            <div style="display:flex; gap:8px;">
              <span style="background:rgba(106,77,247,0.1); color:var(--accent); padding:6px 12px; border-radius:6px; font-weight:600; font-size:12px;">{{ $question->points }} markah</span>
              <span style="background:rgba(106,77,247,0.1); color:var(--accent); padding:6px 12px; border-radius:6px; font-weight:600; font-size:12px;">
                @if($question->type === 'multiple_choice')
                  Pilihan Berganda
                @elseif($question->type === 'short_answer')
                  Jawapan Pendek
                @elseif($question->type === 'true_false')
                  Benar/Salah
                @elseif($question->type === 'checkbox')
                  Kotak Semak
                @else
                  {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                @endif
              </span>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom:20px;">
            <!-- Left: Teks Soalan -->
            <div>
              <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--muted);">Teks Soalan</label>
              <div style="font-size: 14px; line-height:1.6; white-space: pre-wrap; word-wrap: break-word;">{{ $question->question_text }}</div>
            </div>
          </div>

          <!-- Options/Answers Section -->
          <div>
            @if($question->type === 'short_answer')
              <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--success);">Jawapan Betul</label>
                @php
                  $correctOption = $question->options->where('is_correct', true)->first();
                @endphp
                @if($correctOption)
                  <div style="font-size: 14px; font-weight: 600; padding:12px; background:rgba(42, 157, 143, 0.08); border-radius:8px; border-left:3px solid var(--success);">{{ $correctOption->option_text }}</div>
                @else
                  <div style="font-size: 14px; font-weight: 600; padding:12px; background:rgba(200, 200, 200, 0.08); border-radius:8px; border-left:3px solid var(--muted); color:var(--muted);">(Tiada jawapan betul ditetapkan)</div>
                @endif
              </div>

            @elseif($question->type === 'coding')
              <!-- Coding Question Section -->
              <div>
                <!-- Code Template Display -->
                @if ($question->coding_template)
                  <div style="margin-bottom:20px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--muted);">Template Kod:</label>
                    <div style="background:#f5f5f5; padding:12px; border-radius:8px; border:2px solid #d1d5db; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; white-space:pre; overflow-x:auto;">{{ $question->coding_template }}</div>
                  </div>
                @endif

                <!-- Full Code Display with Hidden Lines -->
                @if ($question->coding_full_code)
                  <div style="margin-top:20px;">
                    <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 8px; color:var(--muted);">Kod Lengkap (Garis tersembunyi ditandai):</label>
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
                          <div style="height: 1.5em; display: flex; align-items: center; background: {{ $isHidden ? 'rgba(255, 193, 7, 0.2)' : 'transparent' }}; flex-shrink: 0;">{{ $line }}</div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                @endif
              </div>

            @else
              <!-- Multiple Choice, True/False, Checkbox Options -->
              <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 12px; color:var(--muted);">Pilihan & Jawapan Betul</label>
              <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse($question->options->sortBy('id') as $option)
                  <div style="display:flex; gap:12px; align-items:center; padding:12px; border-radius:8px; border:2px solid {{ $option->is_correct == true || $option->is_correct == 1 ? 'var(--success)' : '#d1d5db' }}; background:{{ $option->is_correct == true || $option->is_correct == 1 ? 'rgba(42, 157, 143, 0.08)' : 'rgba(200, 200, 200, 0.04)' }};">
                    <div style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                      @if($question->type === 'checkbox')
                        @if($option->is_correct == true || $option->is_correct == 1)
                          <span style="color:var(--success); font-weight:bold; font-size:18px;">☑</span>
                        @else
                          <span style="color:var(--muted); font-size:18px;">☐</span>
                        @endif
                      @else
                        @if($option->is_correct == true || $option->is_correct == 1)
                          <span style="color:var(--success); font-weight:bold; font-size:18px;">●</span>
                        @else
                          <span style="color:var(--muted); font-size:18px;">○</span>
                        @endif
                      @endif
                    </div>
                    <div style="flex:1;">{{ $option->option_text }}</div>
                    @if($option->is_correct == true || $option->is_correct == 1)
                      <span style="background:var(--success); color:#fff; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:600; white-space:nowrap;">Betul</span>
                    @endif
                  </div>
                @empty
                  <div style="color:var(--muted); font-size:14px; padding:12px;">Tiada pilihan</div>
                @endforelse
              </div>
            @endif
          </div>
        </section>
      @empty
        <section class="panel" style="text-align:center; padding:40px;">
          <div style="font-size:48px; margin-bottom:12px;">📝</div>
          <div style="color:var(--muted); font-size:14px;">Kuiz ini tidak mempunyai soalan</div>
        </section>
      @endforelse
    </div>
  </main>
</div>

@endsection