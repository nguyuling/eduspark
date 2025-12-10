// resources/views/dashboard/teacher_home.blade.php (CLEANED-UP VERSION)

@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg text-center">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Teacher Dashboard</h2>
                </div>

                <div class="card-body">
                    <h3 class="display-6 mb-4">Welcome back, {{ $user->name }}!</h3>
                    
                    <p class="lead">
                        As a **Teacher**, you can access your modules like Quiz Management and Performance Reports via the navigation bar above.
                    </p>
                    
                    <hr class="my-4">
                    <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-lg btn-success">
                        Go to Quiz Management
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection