@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Kuiz Tersedia</div>
                <div class="sub">Cipta dan uruskan bahan kuiz anda</div>
            </div>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
                ✨ Cipta Kuiz
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <section class="panel panel-spaced" style="margin-top: 60px; background: rgba(42, 157, 143, 0.1); border-left: 3px solid var(--success);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 20px;">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            </section>
        @endif

        @if (session('error'))
            <section class="panel panel-spaced" style="margin-top: 60px; background: rgba(230, 57, 70, 0.1); border-left: 3px solid var(--danger);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 20px;">✕</span>
                    <span>{{ session('error') }}</span>
                </div>
            </section>
        @endif

        <!-- Filter Section -->
        <section class="panel panel-spaced" style="margin-top: 60px;">
            <div class="panel-header">Cari & Tapis</div>

            <form method="GET" action="{{ route('teacher.quizzes.index') }}" style="margin-top: 20px;">
                <div class="filter-form">
                    <div class="form-row">
                        <label for="unique_id" style="margin-bottom: 6px;">ID Kuiz</label>
                        <input 
                            type="text" 
                            id="unique_id" 
                            name="unique_id" 
                            placeholder="Cari ID..."
                            value="{{ request('unique_id') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="title" style="margin-bottom: 6px;">Tajuk Kuiz</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            placeholder="Cari tajuk..."
                            value="{{ request('title') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="creator_email" style="margin-bottom: 6px;">E-mel Pencipta</label>
                        <input 
                            type="email" 
                            id="creator_email" 
                            name="creator_email" 
                            placeholder="guru@email.com"
                            value="{{ request('creator_email') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="publish_date_range" style="margin-bottom: 6px;">Tarikh Terbit</label>
                        <select 
                            id="publish_date_range" 
                            name="publish_date_range"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                            <option value="">Semua Masa</option>
                            <option value="today" {{ request('publish_date_range') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="month" {{ request('publish_date_range') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="3months" {{ request('publish_date_range') === '3months' ? 'selected' : '' }}>3 Bulan Lalu</option>
                            <option value="year" {{ request('publish_date_range') === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">Kosongkan Penapis</a>
                    <button type="submit" class="btn btn-primary">Gunakan Penapis</button>
                </div>
            </form>
        </section>

        <!-- Quizzes Table -->
        <section class="panel panel-spaced" style="margin-top: 20px;">
            <div class="panel-header">Kuiz ({{ $quizzes->count() }})</div>

            <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #d4c5f9;">
                        <th style="padding: 12px; text-align: left; font-weight: 700; color: var(--muted); font-size: 13px;">Butiran Kuiz</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Soalan</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Percubaan</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Tarikh Akhir</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        <tr style="border-bottom: 1px solid #e5e1f2; transition: background 0.2s ease;">
                            <!-- Quiz Details -->
                            <td style="padding: 16px 12px; vertical-align: top;">
                                <div style="font-weight: 700; font-size: 15px; margin-bottom: 6px;">
                                    {{ $quiz->title }}
                                </div>
                                <div style="color: var(--muted); font-size: 13px; margin-bottom: 10px; line-height: 1.4;">
                                    {{ Str::limit($quiz->description, 100) }}
                                </div>
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    @if ($quiz->is_published)
                                        <span class="badge" style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Diterbitkan</span>
                                    @else
                                        <span class="badge" style="background: var(--yellow); color: #0b1220; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Draf</span>
                                    @endif
                                    <span class="badge" style="background: rgba(106, 77, 247, 0.1); color: var(--accent); padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">ID: {{ $quiz->unique_code }}</span>
                                </div>
                            </td>

                            <!-- Questions Count -->
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="font-weight: 700; font-size: 16px;">{{ $quiz->questions_count ?? 0 }}</div>
                            </td>

                            <!-- Attempts Count -->
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="font-weight: 700; font-size: 16px;">{{ $quiz->attempts_count ?? 0 }}</div>
                            </td>

                            <!-- Deadline -->
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; font-size: 13px;">
                                @if ($quiz->due_at)
                                    <div style="{{ $quiz->due_at->isPast() ? 'color: var(--danger);' : '' }}">
                                        {{ $quiz->due_at->format('M d, Y') }}
                                    </div>
                                    <div style="color: var(--muted); font-size: 12px;">{{ $quiz->due_at->format('h:i A') }}</div>
                                @else
                                    <div style="color: var(--muted);">Tiada Tarikh Akhir</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('teacher.quizzes.show', $quiz->id) }}" class="btn btn-small" style="background: transparent; color: var(--accent); border: 1px solid rgba(106, 77, 247, 0.3); padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                        👁️ Lihat
                                    </a>
                                    <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-small" style="background: transparent; color: var(--accent); border: 1px solid rgba(106, 77, 247, 0.3); padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                        ✏️ Sunting
                                    </a>
                                    <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Padam kuiz ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-small" style="background: transparent; color: var(--danger); border: 1px solid rgba(230, 57, 70, 0.3); padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            🗑️ Padam
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center;">
                                <div class="empty-state">
                                    <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
                                    <div style="color: var(--muted); font-size: 16px;">Tiada kuiz ditemui</div>
                                    <div style="color: var(--muted); font-size: 13px; margin-top: 6px;">Klik "Cipta Kuiz" untuk memulai</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if (method_exists($quizzes, 'links'))
                <div style="margin-top: 20px;">
                    {{ $quizzes->links() }}
                </div>
            @endif
        </section>
    </main>
</div>
@endsection