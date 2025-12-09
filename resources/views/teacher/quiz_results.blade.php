@extends('layouts.app') {{-- Assume a master layout file for the teacher panel --}}

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Class Results for: {{ $quiz->title }}</h2>
                </div>

                <div class="card-body">
                    
                    {{-- 1. Class Performance Analysis (UC0012-02) --}}
                    <h4 class="mb-4">Class Performance Overview</h4>
                    <div class="row text-center mb-5">
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-0">Total Submissions</p>
                                <h3 class="text-danger">{{ $statistics['total_students'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-0">Average Score</p>
                                <h3 class="text-primary">{{ $statistics['average'] }}%</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-0">Highest Score</p>
                                <h3 class="text-success">{{ $statistics['highest'] }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 border rounded bg-light">
                                <p class="text-muted mb-0">Lowest Score</p>
                                <h3 class="text-warning">{{ $statistics['lowest'] }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <hr>

                    {{-- 2. Individual Scores Table (UC0012-02) --}}
                    <h4 class="mt-4 mb-3">Individual Student Results</h4>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Attempt #</th>
                                <th>Score</th>
                                <th>Submitted At</th>
                                <th>Remark Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attempts as $attempt)
                                <tr>
                                    <td>{{ $attempt->student->name }}</td>
                                    <td>{{ $attempt->attempt_number }}</td>
                                    <td>
                                        <strong>{{ $attempt->score }} / {{ $quiz->questions->sum('points') }}</strong>
                                    </td>
                                    <td>{{ $attempt->submitted_at->format('M d, H:i') }}</td>
                                    <td>
                                        @if ($attempt->teacher_remark)
                                            <span class="badge bg-info text-dark">Remark Given</span>
                                        @else
                                            <span class="badge bg-warning">Pending Remark</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Link to view detailed results (potentially reused student view) --}}
                                        <a href="{{ route('quizzes.result', $attempt->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                            View Details
                                        </a>

                                        {{-- Button to trigger the modal for giving feedback (UC0012-02) --}}
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#remarkModal"
                                            data-attempt-id="{{ $attempt->id }}"
                                            data-current-remark="{{ $attempt->teacher_remark }}">
                                            Remark
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No submitted attempts for this quiz yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Giving Teacher Feedback/Remark --}}
<div class="modal fade" id="remarkModal" tabindex="-1" aria-labelledby="remarkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarkModalLabel">Add Remark for <span id="student-name-placeholder">Student</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="remark-form" method="POST" action="">
                @csrf
                @method('PUT') {{-- For the update/remark action --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="remark" class="form-label">Teacher's Comment/Remark:</label>
                        <textarea class="form-control" id="remark-input" name="remark" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Remark</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Simple JavaScript to handle modal form action --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const remarkModal = document.getElementById('remarkModal');
        remarkModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const attemptId = button.getAttribute('data-attempt-id');
            const currentRemark = button.getAttribute('data-current-remark');

            // Set the form action to the correct route (you'll need to define this route in routes/web.php)
            // Note: This relies on a JavaScript injection of the Laravel route URL.
            // In a real application, you'd calculate the URL: /teacher/attempts/{attemptId}/remark
            const form = document.getElementById('remark-form');
            form.action = '/teacher/attempts/' + attemptId + '/remark';
            
            // Populate the textarea with the current remark
            document.getElementById('remark-input').value = currentRemark;
        });
    });
</script>
@endsection