@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Keputusan: {{ $quiz->title }}</div>
                <div class="sub">Analisis prestasi kelas dan skor individu</div>
            </div>
        <a href="{{ route('teacher.quizzes.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
        </a>
        </div>

        <!-- Quiz Info -->
        <section class="panel" style="margin-bottom:20px; margin-top:10px;">
            <div class="panel-header">
                <h3>Maklumat Kuiz</h3>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Penerangan</div>
                    <div style="font-size: 14px; line-height: 1.6;">{{ $quiz->description ?? 'Tiada penerangan' }}</div>
                </div>
                <div>
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Butiran Kuiz</div>
                    <div style="font-size: 14px; line-height: 1.8;">
                        <div><strong>Jumlah Soalan:</strong> {{ $quiz->questions_count ?? count($quiz->questions) }}</div>
                        <div><strong>Jumlah Markah:</strong> {{ $totalPoints }}</div>
                        <div><strong>ID Kuiz:</strong> {{ $quiz->unique_code }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Class Performance Overview -->
        <section class="panel" style="margin-top: 20px;">
            <div class="panel-header">
                <h3>Ringkasan Prestasi Kelas</h3>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 20px;">
                <!-- Total Attempts -->
                <div style="padding: 14px; background: rgba(106, 77, 247, 0.05); border-radius: 8px; border-left: 3px solid var(--accent);">
                    <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px;">Jumlah Percubaan</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--accent);">{{ $statistics['total_attempts'] }}</div>
                </div>

                <!-- Average Score -->
                <div style="padding: 14px; background: rgba(42, 157, 143, 0.05); border-radius: 8px; border-left: 3px solid var(--success);">
                    <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px;">Skor Purata</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--success);">{{ $statistics['average'] }}%</div>
                </div>

                <!-- Highest Score -->
                <div style="padding: 14px; background: rgba(76, 175, 80, 0.05); border-radius: 8px; border-left: 3px solid #4caf50;">
                    <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px;">Skor Tertinggi</div>
                    <div style="font-size: 24px; font-weight: 700; color: #4caf50;">{{ $statistics['highest'] }}%</div>
                </div>

                <!-- Lowest Score -->
                <div style="padding: 14px; background: rgba(230, 57, 70, 0.05); border-radius: 8px; border-left: 3px solid var(--danger);">
                    <div style="color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px;">Skor Terendah</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--danger);">{{ $statistics['lowest'] }}%</div>
                </div>
            </div>
        </section>

        <!-- Individual Results Table -->
        <section class="panel" style="margin-top: 20px; margin-bottom: 20px;">
            <div class="panel-header">
                <h3>Keputusan Pelajar - Skor Terbaik</h3>
            </div>

            <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #d4c5f9;">
                        <th style="padding: 12px; text-align: left; font-weight: 700; color: var(--muted); font-size: 13px; width: 30%;">Nama Pelajar</th>
                        <th style="padding: 12px; text-align: left; font-weight: 700; color: var(--muted); font-size: 13px; width: 20%;">E-mel</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px; width: 12%;">Bil. Percubaan</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px; width: 12%;">Skor Terbaik</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px; width: 10%;">Peratusan</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px; width: 16%;">Tarikh Hantar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($studentBestScores as $studentScore)
                        <tr style="border-bottom: 1px solid #e5e1f2;">
                            <td style="padding: 16px 12px; vertical-align: middle; width: 30%;">
                                <div style="font-weight: 600;">{{ $studentScore['student']->name }}</div>
                            </td>
                            <td style="padding: 16px 12px; vertical-align: middle; width: 20%;">
                                <div style="font-size: 13px; color: var(--muted);">{{ $studentScore['student']->email }}</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; width: 12%;">
                                <div style="font-weight: 700;">{{ $studentScore['attempt_count'] }}</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; width: 12%;">
                                <div style="font-weight: 700;">{{ $studentScore['best_score'] }} / {{ $totalPoints }}</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; width: 10%;">
                                <div style="font-weight: 700; font-size: 16px;">{{ $studentScore['percentage'] }}%</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; font-size: 13px; width: 16%;">
                                {{ $studentScore['latest_submitted_at'] ? $studentScore['latest_submitted_at']->format('d M Y H:i') : 'Tiada' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center;">
                                <div class="empty-state">
                                    <div style="color: var(--muted); font-size: 16px;">Tiada penghantaran pelajar lagi</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</div>
@endsection