@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Quiz Results: {{ $quiz->title }}</div>
                <div class="sub">Class performance analysis and individual scores</div>
            </div>
            <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">
                ← Back to Quizzes
            </a>
        </div>

        <!-- Class Performance Overview -->
        <section class="panel panel-spaced" style="margin-top: 60px;">
            <div class="panel-header">Class Performance Overview</div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 20px;">
                <!-- Total Submissions -->
                <div style="padding: 16px; background: rgba(106, 77, 247, 0.05); border-radius: 8px; border-left: 3px solid var(--accent);">
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Total Submissions</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--accent);">{{ $statistics['total_students'] }}</div>
                </div>

                <!-- Average Score -->
                <div style="padding: 16px; background: rgba(42, 157, 143, 0.05); border-radius: 8px; border-left: 3px solid var(--success);">
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Average Score</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--success);">{{ $statistics['average'] }}%</div>
                </div>

                <!-- Highest Score -->
                <div style="padding: 16px; background: rgba(212, 197, 249, 0.05); border-radius: 8px; border-left: 3px solid #d4c5f9;">
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Highest Score</div>
                    <div style="font-size: 28px; font-weight: 700;">{{ $statistics['highest'] }}</div>
                </div>

                <!-- Lowest Score -->
                <div style="padding: 16px; background: rgba(230, 57, 70, 0.05); border-radius: 8px; border-left: 3px solid var(--danger);">
                    <div style="color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px;">Lowest Score</div>
                    <div style="font-size: 28px; font-weight: 700; color: var(--danger);">{{ $statistics['lowest'] }}</div>
                </div>
            </div>
        </section>

        <!-- Individual Results Table -->
        <section class="panel panel-spaced" style="margin-top: 20px;">
            <div class="panel-header">Individual Student Results</div>

            <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #d4c5f9;">
                        <th style="padding: 12px; text-align: left; font-weight: 700; color: var(--muted); font-size: 13px;">Student Name</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Score</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Percentage</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Submission Time</th>
                        <th style="padding: 12px; text-align: center; font-weight: 700; color: var(--muted); font-size: 13px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attempts as $attempt)
                        <tr style="border-bottom: 1px solid #e5e1f2;">
                            <td style="padding: 16px 12px; vertical-align: middle;">
                                <div style="font-weight: 600;">{{ $attempt->user->name }}</div>
                                <div style="color: var(--muted); font-size: 13px;">{{ $attempt->user->email }}</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="font-weight: 700;">{{ $attempt->obtained_marks }} / {{ $quiz->questions->sum('points') }}</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                <div style="font-weight: 700; font-size: 16px;">{{ round(($attempt->obtained_marks / $quiz->questions->sum('points')) * 100) }}%</div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle; font-size: 13px;">
                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                            <td style="padding: 16px 12px; text-align: center; vertical-align: middle;">
                                @if ($attempt->submitted_at)
                                    <span class="badge" style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Submitted</span>
                                @else
                                    <span class="badge" style="background: var(--yellow); color: #0b1220; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">In Progress</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center;">
                                <div class="empty-state">
                                    <div style="font-size: 48px; margin-bottom: 16px;">📊</div>
                                    <div style="color: var(--muted); font-size: 16px;">No student submissions yet</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- Question Analysis -->
        <section class="panel panel-spaced" style="margin-top: 20px;">
            <div class="panel-header">Question Analysis</div>

            <div style="margin-top: 20px;">
                @forelse ($quiz->questions as $question)
                    <div style="background: transparent; border-radius: 12px; border: 2px solid #d4c5f9; padding: 16px; margin-bottom: 12px;">
                        <div style="font-weight: 700; margin-bottom: 12px;">{{ $question->question_text }}</div>
                        <div style="display: flex; gap: 20px; flex-wrap: wrap; font-size: 13px;">
                            <div>
                                <div style="color: var(--muted); font-weight: 600; margin-bottom: 4px;">Correct Answers</div>
                                <div style="font-weight: 700; color: var(--success);">
                                    {{ $question->correct_answers_count ?? 0 }} / {{ $statistics['total_students'] }}
                                </div>
                            </div>
                            <div>
                                <div style="color: var(--muted); font-weight: 600; margin-bottom: 4px;">Success Rate</div>
                                <div style="font-weight: 700;">
                                    {{ $statistics['total_students'] > 0 ? round((($question->correct_answers_count ?? 0) / $statistics['total_students']) * 100) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="color: var(--muted); text-align: center; padding: 20px;">
                        No questions in this quiz
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</div>
@endsection
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