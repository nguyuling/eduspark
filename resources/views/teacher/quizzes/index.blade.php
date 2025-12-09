@extends('layouts.app') {{-- Assume a master layout file for the teacher panel --}}

@section('content')

{{-- Safely initialize $filters --}}
@php
$filters = $filters ?? [];
// Ensure all filter keys are present for default values
$filters = array_merge([
'unique_id' => '',
'title' => '',
'creator_email' => '',
'publish_date_range' => '',
'scope' => 'all'
], $filters);

// The checkbox represents 'mine'. If scope is 'all', it should be unchecked.
// If we receive a query string, we honor that. If not, the default is 'all'.
$isMineChecked = $filters['scope'] === 'mine';


@endphp

<div class="container">
<div class="row justify-content-center">
<div class="col-md-12">

@if (session('success'))

<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- FILTERING CARD (Detailed and Responsive Filter) --}}

<div class="card shadow-sm mb-4">
<div class="card-header bg-light">
    <h5 class="mb-0">Search</h5>
</div>
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

    {{-- Row 2: Checkbox and Actions (Aligned using justify-content-between) --}}
    {{-- UPDATED: Changed justify-content-end to justify-content-between and corrected the structure --}}
    <div class="col-12 d-flex align-items-center justify-content-between pt-3">
        
        {{-- Left Side: Checkbox Filter: "Only show created by me" --}}
        <div class="d-flex align-items-center">
            
            {{-- Hidden field to manage scope (Crucial for unchecked state) --}}
            {{-- NOTE: This hidden input is necessary to ensure the 'scope=all' parameter is sent when unchecked. --}}
            <input type="hidden" name="scope" value="{{ $filters['scope'] }}" id="scope_hidden">

            <div class="form-check">
                {{-- FIX: Added me-2 class for spacing and corrected broken structure --}}
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
            {{-- UPDATED: Route fixed to 'teacher.quizzes.index' and styling set to btn-light border for subtle look --}}
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
<a href="{{ route(name: 'teacher.quizzes.create') }}" class="btn btn-lg btn-success">
<i class="bi bi-plus-circle"></i> Create New Quiz
</a>
</div>

<div class="card shadow-lg">
<div class="card-header bg-primary text-white">
<h2>Quizzes Available </h2>
</div>

<div class="card-body">

{{-- Check if any filters are active --}}
@if (!empty(array_filter(array_diff_key($filters, array_flip(['scope'])))) || $isMineChecked === true) {{-- Check if any filter is active OR if 'mine' scope is active --}}

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
                {{-- ADJUSTED: Used flex-wrap for better responsiveness on small screens --}}
                <div class="mt-1 d-flex flex-wrap align-items-center">
                    
                    {{-- Status Badge (Increased size: p-2, fw-bold) --}}
                    @if ($quiz->is_published)
                    @else
                        <span class="badge bg-warning text-dark fw-bold p-2 me-2 mb-1">Draft</span>
                    @endif
                    
                    {{-- Creator Badge (Increased size: p-2) --}}
                    <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary">
                        Creator: {{ $quiz->creator->name ?? 'N/A' }}
                    </span>
                    
                    {{-- ID Badge (Increased size: p-2) --}}
                    <span class="badge bg-light text-dark fw-normal p-2 me-2 mb-1 border border-secondary d-flex align-items-center">
                        ID: <strong class="ms-1">{{ $quiz->unique_code ?? 'N/A' }}</strong>
                    </span>
                    
                    {{-- Improved Copy ID Button (New style: btn-info, added icon, custom padding) --}}
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
        
        <td class="text-center text-nowrap">
            <a href="{{ route('teacher.quizzes.results', $quiz->id) }}" class="btn btn-sm btn-info me-1 shadow" title="View Results">
                <i class="bi bi-bar-chart-fill"></i> Results
            </a>

            <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="btn btn-sm btn-secondary me-1 shadow" title="Edit Quiz">
                <i class="bi bi-pencil"></i> Edit
            </a>
            
            <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger shadow" 
                        onclick="return confirm('Are you sure you want to delete the quiz: {{ $quiz->title }}? This action is irreversible and deletes all student attempts.');" title="Delete Quiz">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4">
            @if (!empty(array_filter($filters)))
                No quizzes matched your search criteria.
            @else
                You haven't created any quizzes yet. Click "Create New Quiz" to start.
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

Copies the unique quiz code to the clipboard and provides visual feedback.
*/
function copyQuizId(button) {
const code = button.getAttribute('data-code');
// ... (rest of the copyQuizId function remains the same)
if (navigator.clipboard) {
navigator.clipboard.writeText(code).then(() => {
const originalText = button.innerHTML;
button.innerHTML = 'Copied!';
// FIX: Remove the new original class (btn-info) before adding success
button.classList.remove('btn-warning', 'text-dark'); // Corrected original classes
button.classList.add('btn-success');

     setTimeout(() => {
         button.innerHTML = originalText;
         button.classList.remove('btn-success');
         // FIX: Revert back to the original classes
         button.classList.add('btn-warning', 'text-dark'); 
     }, 1500);
 }).catch(err => {
     console.error('Could not copy text: ', err);
     console.warn("Manual copy fallback needed: " + code);
 });


} else {
console.warn("Manual copy fallback needed: " + code);
}
}
</script>

@endsection