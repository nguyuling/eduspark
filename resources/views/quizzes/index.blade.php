@extends('layouts.app')

@if (session('error'))

<div class="alert alert-danger">{{ session('error') }}</div>
@endif

@section('content')

<div class="container">
<div class="row justify-content-center">
<div class="col-md-12">

{{-- FILTERING CARD (Search form) --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Search</h5>
    </div>
    <div class="card-body">
        {{-- Form uses GET method to pass filters via URL query parameters --}}
        <form method="GET" action="{{ route('student.quizzes.index') }}">
            <div class="row g-3">
                
                {{-- ROW 1: Four Main Filters (Unique ID, Title, Creator, Publish Date) --}}

                {{-- Filter 1: Unique ID --}}
                <div class="col-sm-6 col-md-3">
                    <label for="unique_id" class="form-label small text-muted">Unique ID</label>
                    <input type="text" class="form-control form-control-sm" id="unique_id" name="unique_id" 
                           value="{{ $filters['unique_id'] ?? '' }}" placeholder="A1b2C3d4">
                </div>

                {{-- Filter 2: Title Keyword --}}
                <div class="col-sm-6 col-md-3">
                    <label for="title" class="form-label small text-muted">Title Keyword</label>
                    <input type="text" class="form-control form-control-sm" id="title" name="title" 
                           value="{{ $filters['title'] ?? '' }}" placeholder="Computer Science">
                </div>

                {{-- Filter 3: Creator Email --}}
                <div class="col-sm-6 col-md-3">
                    <label for="creator_email" class="form-label small text-muted">Creator Email</label>
                    <input type="email" class="form-control form-control-sm" id="creator_email" name="creator_email" 
                           value="{{ $filters['creator_email'] ?? '' }}" placeholder="teacher@email.com">
                </div>

                {{-- Filter 4: Publish Date --}}
                <div class="col-sm-6 col-md-3">
                    <label for="publish_date" class="form-label small text-muted">Publish Date</label>
                    <select class="form-select form-select-sm" id="publish_date" name="publish_date">
                        <option value="">All Time</option>
                        <option value="today" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'today') selected @endif>Today</option>
                        <option value="this_month" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_month') selected @endif>This Month</option>
                        <option value="3_months" @if (isset($filters['publish_date']) && $filters['publish_date'] == '3_months') selected @endif>Last 3 Months</option>
                        <option value="this_year" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_year') selected @endif>This Year</option>
                    </select>
                </div>

                {{-- ROW 2: Checkbox and Action Buttons --}}

                {{-- The main container uses d-flex and justify-content-between to push children to the far ends --}}
                <div class="col-12 d-flex align-items-center justify-content-between pt-2"> 
                    
                    {{-- Left Side: Filter 5 (Attempted) --}}
                    <div class="form-check">
                        <input class="form-check-input me-2" type="checkbox" id="attempted" name="attempted" value="1"
                            @if (isset($filters['attempted']) && $filters['attempted'] == '1') checked @endif>
                        <label class="form-check-label" for="attempted">
                            Only show Quizzes I have attempted
                        </label>
                    </div>
                    
                    {{-- Right Side: Action Buttons (Clear and Apply) --}}
                    {{-- Removed the redundant col-md-6 and text-end class as justify-content-between handles the spacing --}}
                    <div class="text-end">
                        {{-- Link to clear filters --}}
                        <a href="{{ route('student.quizzes.index') }}" class="btn btn-sm btn-light border me-2 shadow">Clear Filters</a>
                        <button type="submit" class="btn btn-sm btn-primary shadow">Apply Filters</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- END FILTERING CARD --}}

<div class="card shadow-lg">
<div class="card-header bg-primary text-white">
<h2>Quizzes Available</h2>
</div>

        <div class="card-body">
            {{-- Check if filters were applied and display a message --}}
            @if (!empty(array_filter($filters)))
                <div class="alert alert-info py-2 small">
                    Showing results filtered by your criteria. Total quizzes found: <strong>{{ $quizzes->count() }}</strong>
                </div>
            @endif
            
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 40%;">Quiz Details</th> 
                        <th class="text-center" style="width: 25%;">Attempts</th> 
                        <th class="text-center" style="width: 15%;">Score</th>
                        <th class="text-center" style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quizzes as $quiz)
                        @php
                            // Get attempts made by the current student (already filtered by the controller)
                            $completedAttempts = $quiz->attempts;
                            $attemptsUsed = $completedAttempts->count();
                            $isFutureDue = !$quiz->due_at || $quiz->due_at->isFuture();
                            $canAttempt = ($attemptsUsed < $quiz->max_attempts) && $isFutureDue;
                            
                            // Get the most recent attempt
                            $latestAttempt = $completedAttempts->sortByDesc('submitted_at')->first();
                            
                            // Determine Status badge
                            $statusBadge = 'New';
                            $statusClass = 'text-secondary';
                            if ($latestAttempt) {
                                $statusBadge = 'Completed';
                                $statusClass = 'text-success';
                            } elseif ($quiz->due_at && $quiz->due_at->isPast()) {
                                $statusBadge = 'Due';
                                $statusClass = 'text-danger';
                            }
                        @endphp
                        
                        <tr>
                            {{-- 1. QUIZ TITLE, DESCRIPTION, CREATOR & ID --}}
                            <td style="width: 40%;">
                                {{-- Title and description --}}
                                <strong class="h5 d-block mb-1">{{ $quiz->title }}</strong>
                                <p class="small text-muted mt-0 mb-1">{{ $quiz->description }}</p>

                                {{-- Creator and Unique ID as small, compact badges --}}
                                <div class="mt-1 d-flex flex-wrap align-items-center">
                                    {{-- Creator Badge --}}
                                    <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary">
                                        Creator: {{ $quiz->creator->name ?? 'N/A' }}
                                    </span>
                                    
                                    {{-- ID Badge --}}
                                    <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary d-flex align-items-center">
                                        ID: <strong class="ms-1">{{ $quiz->unique_code ?? 'N/A' }}</strong>
                                    </span>
                                    
                                    {{-- Improved Copy ID Button - UPDATED for yellow color and border --}}
                                    <button type="button" 
                                        class="btn btn-sm btn-warning text-dark py-1 px-3 mb-1 shadow"
                                        data-code="{{ $quiz->unique_code }}"
                                        onclick="copyQuizId(this)">
                                        <i class="bi bi-clipboard-check-fill me-1"></i> Copy ID
                                    </button>
                                </div>
                            </td>
                            
                            {{-- 2. AVAILABILITY & ATTEMPTS (COMBINED) --}}
                            <td class="text-center small" style="width: 25%;">
                                <p class="mb-1">
                                    Attempts Used: <strong>{{ $attemptsUsed }}</strong> / {{ $quiz->max_attempts }}
                                </p>
                                <p class="mb-0">
                                    @if ($quiz->due_at)
                                        @if ($quiz->due_at->isPast())
                                            <span class="text-danger">Deadline: {{ $quiz->due_at->format('M d, Y h:i A') }}</span>
                                        @else
                                            <span class="text-success">Due: {{ $quiz->due_at->format('M d, Y h:i A') }}</span>
                                        @endif
                                    @else
                                        <span class="text-secondary">No Deadline</span>
                                    @endif
                                </p>
                            </td>

                            {{-- 3. STATUS / SCORE (FIXED TOTAL MARKS CALCULATION) --}}
                            <td class="text-center" style="width: 15%;">
                                {{-- Updated: added d-block to force the score underneath the badge --}}
                                <span class="badge {{ $statusClass }} fw-normal fw-bold p-1 me-1 mb-1" style="width: fit-content;">{{ $statusBadge }}</span>
                                @if ($latestAttempt)
                                    @php
                                        $totalMarks = $quiz->questions->sum('points') ?? 0;
                                    @endphp
                                    <strong class="d-block mt-1">
                                        Score: {{ $latestAttempt->score }}/{{ $totalMarks }}
                                    </strong>
                                @endif
                            </td>
                            
                            {{-- 4. ACTION --}}
                            <td class="text-center" style="width: 20%;">
                                @if ($canAttempt)
                                    <a href="{{ route('student.quizzes.attempt.start', $quiz->id) }}" class="btn btn-sm btn-primary py-2 px-3 mb-1 shadow">
                                        {{ $attemptsUsed > 0 ? 'Re-attempt Quiz' : 'Start Quiz' }}
                                    </a>
                                @elseif ($latestAttempt)
                                    {{-- If attempts are used up or expired, but an attempt exists, allow viewing the result --}}
                                    <a href="{{ route('student.quizzes.result', $latestAttempt->id) }}" class="btn btn-sm btn-info">
                                        View Result
                                    </a>
                                @else
                                    <span class="text-danger small">Unavailable</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                @if (!empty(array_filter($filters)))
                                    No quizzes matched your search criteria.
                                @else
                                    There are currently no published quizzes available for you.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


</div>

</div>
{{-- NOTE: Missing JavaScript for copyQuizId function, which is required for the "Copy ID" button to work. 
    I will include a basic placeholder script here to prevent reference errors, assuming the actual function
    was meant to be at the end of the file, similar to the teacher index view. --}}
<script>
/**
* Copies the unique quiz code to the clipboard and provides visual feedback.
* This function is included as a placeholder for completeness, as it's required by the HTML.
*/
function copyQuizId(button) {
    const code = button.getAttribute('data-code');
    // Store original classes, including the new 'btn-warning text-dark border border-secondary'
    const originalClasses = 'btn btn-sm btn-warning text-dark border border-secondary py-1 px-3 mb-1';
    const originalHtml = button.innerHTML;

    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(() => {
            // Success state
            button.innerHTML = '<i class="bi bi-check-lg me-1"></i> Copied!';
            // Use btn-success for the temporary success state
            button.className = 'btn btn-sm btn-success py-1 px-3 mb-1'; 

            setTimeout(() => {
                // Revert to original state
                button.innerHTML = originalHtml;
                button.className = originalClasses;
            }, 1500);
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    } else {
        console.warn("Clipboard access denied. Manual copy fallback needed: " + code);
    }
}
</script>
@endsection