@extends('layouts.app')

@section('content')

<div class="app">
    <!-- Main -->
    <main class="main">
        <div class="header">
            <div>
                <div>
                    <div class="title">{{ $attempt->quiz->title }}</div>
                    <div class="sub">Keputusan percubaan kuiz anda</div>
                </div>
            </div>
            <a href="{{ route('student.quizzes.index') }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i>Kembali
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
        <section style="margin-top:40px; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px; font-weight:700;">Ulasan Jawapan</h2>
        </section>

        <div id="answers-container">
            @forelse($attempt->answers as $index => $answer)
                <section class="panel" style="margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #d4c5f9;">
                        <h3 style="margin:0; font-size:16px; font-weight:700;">Soalan {{ $index + 1 }}</h3>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <span style="background:{{ $answer->is_correct ? 'rgba(42, 157, 143, 0.1)' : 'rgba(230, 57, 70, 0.1)' }}; color:{{ $answer->is_correct ? 'var(--success)' : 'var(--danger)' }}; padding:6px 12px; border-radius:6px; font-weight:600; font-size:12px;">{{ $answer->score_gained ?? 0 }}/{{ $answer->question->points }} markah</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom:20px;">
                        <!-- Left: Teks Soalan -->
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--muted);">Teks Soalan</label>
                            <div style="font-size: 14px; line-height:1.6; white-space: pre-wrap; word-wrap: break-word;">{{ $answer->question->question_text }}</div>
                        </div>
                    </div>

                    <!-- Student's Answer Section -->
                    <div style="margin-bottom:20px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--muted);">Jawapan Anda</label>
                        <div>
                            @if($answer->question->type === 'short_answer')
                                <div style="font-size: 14px; font-weight: 600; padding:12px; background:rgba(200, 200, 200, 0.04); border-radius:8px; border-left:3px solid {{ $answer->is_correct ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ ($answer->submitted_text === null || $answer->submitted_text === '') ? '(Tidak dijawab)' : $answer->submitted_text }}
                                </div>

                            @elseif($answer->question->type === 'coding')
                                @php
                                    $submittedAnswers = json_decode($answer->submitted_text, true) ?? [];
                                    $hiddenLineNumbers = !empty($answer->question->hidden_line_numbers) 
                                        ? array_map('intval', explode(',', $answer->question->hidden_line_numbers))
                                        : [];
                                    $codeLines = explode("\n", $answer->question->coding_full_code);
                                @endphp
                                @if(count($codeLines) > 0)
                                    <div style="position: relative; background: #f5f5f5; border-radius: 8px; border: 2px solid #d1d5db; overflow: hidden; padding:0; min-height:100px; display: flex;">
                                        <!-- Line Numbers Column -->
                                        <div style="flex-shrink: 0; width: 40px; background: #e8e8e8; padding: 8px 0; text-align: right; font-size: 12px; font-family: 'Courier New', monospace; color: #888; border-right: 1px solid #d1d5db; line-height: 1.5; user-select: none; padding-right: 6px; display: flex; flex-direction: column;">
                                            @foreach ($codeLines as $lineIndex => $line)
                                                <div style="height: 1.5em; display: flex; align-items: center; justify-content: flex-end;">{{ $lineIndex + 1 }}</div>
                                            @endforeach
                                        </div>
                                        <!-- Code Content Column -->
                                        <div style="flex: 1; padding: 8px 8px; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; color:inherit; white-space: pre; overflow-x: auto; display: flex; flex-direction: column;">
                                            @foreach ($codeLines as $lineIndex => $codeLine)
                                                @php
                                                    $lineNum = $lineIndex + 1;
                                                    $isHidden = in_array($lineNum, $hiddenLineNumbers);
                                                    $lineKey = 'line_' . $lineNum;
                                                    $studentAnswer = $submittedAnswers[$lineKey] ?? '';
                                                    $expectedCode = trim($codeLine);
                                                    $isCorrect = trim($studentAnswer) === $expectedCode;
                                                @endphp
                                                <div style="height: 1.5em; display: flex; align-items: center; background: {{ $isHidden ? ($isCorrect ? 'rgba(42,157,143,0.15)' : 'rgba(230,57,70,0.15)') : 'transparent' }}; flex-shrink: 0;">{{ $isHidden ? ($studentAnswer ?: '(Walang sagot)') : $codeLine }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div style="font-size:14px; color:#0b1220;">(Tidak dijawab)</div>
                                @endif

                            @else
                                <!-- Multiple Choice, True/False, Checkbox Options -->
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    @php
                                        $selectedOptions = $answer->options ?? collect();
                                        $correctOptions = $answer->question->options->where('is_correct', true);
                                    @endphp
                                    @forelse($answer->question->options->sortBy('id') as $option)
                                        @php
                                            $isSelected = $selectedOptions->contains('id', $option->id);
                                            $isCorrectAnswer = $option->is_correct == true || $option->is_correct == 1;
                                        @endphp
                                        <div style="display:flex; gap:12px; align-items:center; padding:12px; border-radius:8px; border:2px solid {{ ($isSelected && $isCorrectAnswer) || ($isCorrectAnswer && !$answer->is_correct) ? 'var(--success)' : ($isSelected && !$isCorrectAnswer ? 'var(--danger)' : '#d1d5db') }}; background:{{ ($isSelected && $isCorrectAnswer) || ($isCorrectAnswer && !$answer->is_correct) ? 'rgba(42, 157, 143, 0.08)' : ($isSelected && !$isCorrectAnswer ? 'rgba(230, 57, 70, 0.08)' : 'rgba(200, 200, 200, 0.04)') }}; position:relative;">
                                            <div style="width:24px; height:24px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                @if($answer->question->type === 'checkbox')
                                                    @if($isSelected)
                                                        <span style="color:{{ $isCorrectAnswer ? 'var(--success)' : 'var(--danger)' }}; font-weight:bold; font-size:18px;">☑</span>
                                                    @else
                                                        <span style="color:{{ $isCorrectAnswer ? 'var(--success)' : 'var(--muted)' }}; font-size:18px;">{{ $isCorrectAnswer ? '☑' : '☐' }}</span>
                                                    @endif
                                                @else
                                                    @if($isSelected)
                                                        <span style="color:{{ $isCorrectAnswer ? 'var(--success)' : 'var(--danger)' }}; font-weight:bold; font-size:18px;">●</span>
                                                    @else
                                                        <span style="color:{{ $isCorrectAnswer ? 'var(--success)' : 'var(--muted)' }}; font-size:18px;">{{ $isCorrectAnswer ? '●' : '○' }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                            <div style="flex:1;">{{ $option->option_text }}</div>
                                            @if($isSelected && !$isCorrectAnswer)
                                                <span style="background:var(--danger); color:#fff; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:600; white-space:nowrap;">Salah</span>
                                            @elseif($isCorrectAnswer)
                                                <span style="background:var(--success); color:#fff; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:600; white-space:nowrap;">{{ ($answer->question->type === 'checkbox' && $isSelected) ? 'Dipilih' : 'Betul' }}</span>
                                            @endif
                                        </div>
                                    @empty
                                        <div style="color:var(--muted); font-size:14px; padding:12px;">Tiada pilihan</div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Correct Answer (show for short answer or coding when wrong) -->
                    @if(!$answer->is_correct && ($answer->question->type === 'short_answer' || $answer->question->type === 'coding'))
                        <div style="margin-bottom:20px;">
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color:var(--muted);">Jawapan yang Betul</label>
                            
                            @if($answer->question->type === 'short_answer')
                                <div style="font-size: 14px; font-weight: 600; padding:12px; background:rgba(42, 157, 143, 0.08); border-radius:8px; border-left:3px solid var(--success);">
                                    {{ ($answer->question->correct_answer === null || $answer->question->correct_answer === '') ? '(Tiada jawapan betul ditakrifkan)' : $answer->question->correct_answer }}
                                </div>
                            @elseif($answer->question->type === 'coding')
                                @php
                                    $codeLines = explode("\n", $answer->question->coding_full_code);
                                    $hiddenLineNumbers = !empty($answer->question->hidden_line_numbers) 
                                        ? array_map('intval', explode(',', $answer->question->hidden_line_numbers))
                                        : [];
                                @endphp
                                <div style="position: relative; background: #f5f5f5; border-radius: 8px; border: 2px solid #06a77d; overflow: hidden; padding:0; min-height:100px; display: flex;">
                                    <!-- Line Numbers Column -->
                                    <div style="flex-shrink: 0; width: 40px; background: #e8e8e8; padding: 8px 0; text-align: right; font-size: 12px; font-family: 'Courier New', monospace; color: #888; border-right: 1px solid #d1d5db; line-height: 1.5; user-select: none; padding-right: 6px; display: flex; flex-direction: column;">
                                        @foreach ($codeLines as $lineIndex => $line)
                                            <div style="height: 1.5em; display: flex; align-items: center; justify-content: flex-end;">{{ $lineIndex + 1 }}</div>
                                        @endforeach
                                    </div>
                                    <!-- Code Content Column -->
                                    <div style="flex: 1; padding: 8px 8px; font-family:'Courier New', monospace; font-size:12px; line-height:1.5; color:inherit; white-space: pre; overflow-x: auto; display: flex; flex-direction: column;">
                                        @foreach ($codeLines as $lineIndex => $codeLine)
                                            @php
                                                $lineNum = $lineIndex + 1;
                                                $isHidden = in_array($lineNum, $hiddenLineNumbers);
                                            @endphp
                                            <div style="height: 1.5em; display: flex; align-items: center; background: {{ $isHidden ? 'rgba(42,157,143,0.2)' : 'transparent' }}; flex-shrink: 0;">{{ $codeLine }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </section>
            @empty
                <section class="panel" style="text-align:center; padding:40px;">
                    <div style="font-size:48px; margin-bottom:12px;">📝</div>
                    <div style="color:var(--muted); font-size:14px;">Tiada jawapan ditemui.</div>
                </section>
            @endforelse
        </div>
    </main>
</div>

@endsection