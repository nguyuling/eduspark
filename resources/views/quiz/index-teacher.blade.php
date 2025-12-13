@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Manage Quizzes</div>
                <div class="sub">Create and manage your quiz materials</div>
            </div>
            <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
                ✨ Create New Quiz
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
            <div class="panel-header">Search & Filter</div>

            <form method="GET" action="{{ route('teacher.quizzes.index') }}" style="margin-top: 20px;">
                <div class="filter-form">
                    <div class="form-row">
                        <label for="unique_id" style="margin-bottom: 6px;">Quiz ID</label>
                        <input 
                            type="text" 
                            id="unique_id" 
                            name="unique_id" 
                            placeholder="Search ID..."
                            value="{{ request('unique_id') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="title" style="margin-bottom: 6px;">Quiz Title</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            placeholder="Search title..."
                            value="{{ request('title') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="creator_email" style="margin-bottom: 6px;">Creator Email</label>
                        <input 
                            type="email" 
                            id="creator_email" 
                            name="creator_email" 
                            placeholder="teacher@email.com"
                            value="{{ request('creator_email') }}"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                    </div>

                    <div class="form-row">
                        <label for="publish_date_range" style="margin-bottom: 6px;">Publish Date</label>
                        <select 
                            id="publish_date_range" 
                            name="publish_date_range"
                            style="width: 100%; padding: 11px 14px; border-radius: 8px; border: 1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size: 14px; outline: none;"
                        >
                            <option value="">All Time</option>
                            <option value="today" {{ request('publish_date_range') === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="month" {{ request('publish_date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="3months" {{ request('publish_date_range') === '3months' ? 'selected' : '' }}>Last 3 Months</option>
                            <option value="year" {{ request('publish_date_range') === 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </section>

        <!-- Quizzes Table -->
        <section class="panel panel-spaced" style="margin-top: 20px;">
            <div class="panel-header">Quizzes ({{ $quizzes->count() }})</div>

            <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #d4c5f9;">
                        <th style="padding: 12px; text-align: left; font-weight: 700; color: var(--muted); font-size: 13px;">Quiz Details</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Questions</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Attempts</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Deadline</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Actions</th>
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
                                        <span class="badge" style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Published</span>
                                    @else
                                        <span class="badge" style="background: var(--yellow); color: #0b1220; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Draft</span>
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
                                    <div style="color: var(--muted);">No Deadline</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('teacher.quizzes.show', $quiz->id) }}" class="btn btn-small" style="background: transparent; color: var(--accent); border: 1px solid rgba(106, 77, 247, 0.3); padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                        👁️ View
                                    </a>
                                    <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-small" style="background: transparent; color: var(--accent); border: 1px solid rgba(106, 77, 247, 0.3); padding: 6px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this quiz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-small" style="background: transparent; color: var(--danger); border: 1px solid rgba(230, 57, 70, 0.3); padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                            🗑️ Delete
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
                                    <div style="color: var(--muted); font-size: 16px;">No quizzes found</div>
                                    <div style="color: var(--muted); font-size: 13px; margin-top: 6px;">Click "Create New Quiz" to get started</div>
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
    <div class="card-body">
        <form method="GET" action="{{ route('teacher.quizzes.index') }}">
            <div class="row g-3">

                {{-- Row 1: Four Input Fields in one row --}}
                
                {{-- Unique ID --}}
                <div class="col-md-3 col-sm-6">
                    <label for="unique_id" class="form-label small text-muted mb-1">Unique ID</label>
                    <input type="text" class="form-control form-control-sm" id="unique_id" name="unique_id" 
                           value="{{ $filters['unique_id'] }}" placeholder="A1b2C3d4">
                </div>
                
                {{-- Title Keyword --}}
                <div class="col-md-3 col-sm-6">
                    <label for="title" class="form-label small text-muted mb-1">Title Keyword</label>
                    <input type="text" class="form-control form-control-sm" id="title" name="title" 
                           value="{{ $filters['title'] }}" placeholder="Computer Science">
                </div>
                
                {{-- Creator Email --}}
                <div class="col-md-3 col-sm-6">
                    <label for="creator_email" class="form-label small text-muted mb-1">Creator Email</label>
                    <input type="email" class="form-control form-control-sm" id="creator_email" name="creator_email" 
                           value="{{ $filters['creator_email'] }}" placeholder="teacher@email.com">
                </div>

                {{-- Publish Date Range --}}
                <div class="col-md-3 col-sm-6">
                    <label for="publish_date_range" class="form-label small text-muted mb-1">Publish Date</label>
                    <select class="form-select form-select-sm" id="publish_date_range" name="publish_date_range">
                        <option value="">All Time</option>
                        <option value="today" @selected($filters['publish_date_range'] === 'today')>Today</option>
                        <option value="month" @selected($filters['publish_date_range'] === 'month')>This Month</option>
                        <option value="3months" @selected($filters['publish_date_range'] === '3months')>Last 3 Months</option>
                        <option value="year" @selected($filters['publish_date_range'] === 'year')>This Year</option>
                    </select>
                </div>

                {{-- Row 2: Checkbox and Actions --}}
                <div class="col-12 d-flex align-items-center justify-content-between pt-3">
                    
                    {{-- Left Side: Checkbox Filter: "Only show created by me" --}}
                    <div class="d-flex align-items-center">
                        
                        {{-- Hidden field to manage scope (Crucial for unchecked state) --}}
                        <input type="hidden" name="scope" value="{{ $filters['scope'] }}" id="scope_hidden">

                        <div class="form-check">
                            <input class="form-check-input me-2" type="checkbox" id="scope_mine" 
                                   value="mine"
                                   @checked($isMineChecked)
                                   onchange="document.getElementById('scope_hidden').value = this.checked ? 'mine' : 'all'">
                            <label class="form-check-label" for="scope_mine">
                                Only show created by me
                            </label>
                        </div>
                    </div>

                    {{-- Right Side: Action Buttons (Clear and Apply) --}}
                    <div class="text-end">
                        {{-- Link to clear filters --}}
                        <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-sm btn-light border me-2 shadow">Clear Filters</a>
                        <button type="submit" class="btn btn-sm btn-primary shadow">Apply Filters</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- END FILTERING CARD --}}

<div class="d-flex justify-content-end align-items-center mb-4">
    <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-lg btn-success">
        <i class="bi bi-plus-circle"></i> Create New Quiz
    </a>
</div>

<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h2>Quizzes Available </h2>
    </div>

    <div class="card-body">

        {{-- Check if any filters are active --}}
        @if (!empty(array_filter(array_diff_key($filters, array_flip(['scope'])))) || $isMineChecked === true)
            <div class="alert alert-info py-2 small">
                Showing filtered results. Total quizzes found: <strong>{{ $quizzes->total() }}</strong>
            </div>
        @endif

        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Quiz Details</th>
                    <th class="text-center">Questions</th>
                    <th class="text-center">Attempts</th>
                    <th class="text-center">Deadline</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
            @forelse ($quizzes as $quiz)
                <tr>
                    <td>
                        <strong class="h5 d-block mb-1">{{ $quiz->title }}</strong>
                        <p class="small text-muted mt-0 mb-1">{{ $quiz->description }}</p>

                        @if ($quiz->is_published)
                            <div class="mt-1 d-flex flex-wrap align-items-center">
                                
                                {{-- Status Badge (Removed duplicate logic, only shows if published is TRUE) --}}
                                <span class="badge bg-success text-white fw-bold p-2 me-2 mb-1">Published</span>
                                
                                {{-- Creator Badge --}}
                                <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary">
                                    Creator: {{ $quiz->creator->name ?? 'N/A' }}
                                </span>
                                
                                {{-- ID Badge --}}
                                <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary d-flex align-items-center">
                                    ID: <strong class="ms-1">{{ $quiz->unique_code ?? 'N/A' }}</strong>
                                </span>
                                
                                {{-- Copy ID Button --}}
                                <button type="button" 
                                    class="btn btn-sm btn-warning text-dark py-1 px-3 mb-1 shadow"
                                    data-code="{{ $quiz->unique_code }}"
                                    onclick="copyQuizId(this)">
                                    <i class="bi bi-clipboard-check-fill me-1"></i> Copy ID
                                </button>
                            </div>
                        @else 
                            {{-- Display Draft Status for unpublished quizzes --}}
                            <div class="mt-1 d-flex flex-wrap align-items-center">
                                <span class="badge bg-warning text-dark fw-bold p-2 me-2 mb-1">Draft</span>
                            </div>
                        @endif
                    </td>
                    
                    <td class="text-center">
                        <strong>{{ $quiz->questions_count }}</strong>
                    </td>
                    
                    <td class="text-center">
                        <strong>{{ $quiz->attempts_count ?? 0 }}</strong>
                    </td>
                    
                    <td class="text-center">
                        @if ($quiz->due_at)
                            <span class="{{ $quiz->due_at->isPast() ? 'text-danger' : '' }}">
                                {{ $quiz->due_at->format('M d, Y') }}<br>
                                <small>{{ $quiz->due_at->format('h:i A') }}</small>
                            </span>
                        @else
                            <span class="text-secondary">No Deadline</span>
                        @endif
                    </td>
                    
                    {{-- START OF ACTIONS COLUMN WITH PERMISSION CHECK --}}
                    <td class="text-center text-nowrap">
                        
                        {{-- Results Button (Always visible) --}}
                        <a href="{{ route('teacher.quizzes.results', $quiz->id) }}" class="btn btn-sm btn-info me-1 shadow" title="View Results">
                            <i class="bi bi-bar-chart-fill"></i> Results
                        </a>
                        
                        {{-- HIDE/SHOW EDIT & DELETE BUTTONS BASED ON OWNERSHIP --}}
                        @if (Auth::id() === $quiz->teacher_id)
                            
                            {{-- 1. EDIT BUTTON --}}
                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-sm btn-info me-2" title="Edit Quiz">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            {{-- 2. DELETE BUTTON (using a form for POST/DELETE method) --}}
                            <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this quiz? All associated data will be lost.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Quiz">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        @else
                            {{-- Optional: Show a view/detail button if not the owner --}}
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-sm btn-secondary" title="View Details">
                                <i class="fas fa-eye"></i> View
                            </a>
                        @endif
                    </td>
                    {{-- END OF ACTIONS COLUMN WITH PERMISSION CHECK --}}
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        @if (!empty(array_filter($filters)))
                            No quizzes matched your search criteria.
                        @else
                            No quizzes found in the system. Click "Create New Quiz" to start.
                        @endif
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            @if (method_exists($quizzes, 'links'))
                {{ $quizzes->links() }}
            @endif
        </div>

    </div>
</div>

</div>
</div>
</div>

{{-- JavaScript for Copy Functionality --}}
<script>
    /**
     * Copies the unique quiz code to the clipboard and provides visual feedback.
     */
    function copyQuizId(button) {
        const code = button.getAttribute('data-code');
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = 'Copied!';
                
                // Correctly handle button class switching
                button.classList.remove('btn-warning', 'text-dark');
                button.classList.add('btn-success');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-warning', 'text-dark'); 
                }, 1500);
            }).catch(err => {
                console.error('Could not copy text: ', err);
                console.warn("Manual copy fallback needed: " + code);
            });
        } else {
            console.warn("Clipboard API not available. Manual copy needed: " + code);
        }
    }
</script>

@endsection