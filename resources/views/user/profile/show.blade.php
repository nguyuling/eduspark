@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <h1 class="mb-4 fw-bold text-primary">My Profile</h1>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-3 text-secondary"></i>
                    <h5 class="mb-0 text-dark">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">Full Name</p>
                            <p class="fw-bold">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">Email</p>
                            <p class="fw-bold">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">Phone</p>
                            <p class="fw-bold">{{ $user->phone ?: '—' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">Role</p>
                            <p class="fw-bold">{{ $user->role === 'teacher' ? 'Teacher' : 'Student' }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top">
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary float-end">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark"><i class="bi bi-building me-2"></i> Organization Details</h5>
                </div>
                <div class="card-body">
                    @php
                        $schoolNames = ['JEA3060' => 'SMK Pengerang Utama', 'JEA3061' => 'SMK Pengerang'];
                        $schoolName = $schoolNames[$user->school_code] ?? $user->school_code;
                    @endphp
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">District</p>
                            <p class="fw-bold">{{ $user->district }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">School</p>
                            <p class="fw-bold">{{ $schoolName }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">Member Since</p>
                            <p class="fw-bold">{{ $user->created_at->format('j M Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-muted mb-1 small">User ID</p>
                            <p class="fw-bold badge bg-secondary">{{ $user->user_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-dark"><i class="bi bi-shield-lock me-2"></i> Account Security</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <p class="mb-0">Password is set and secured.</p>
                    <a href="{{ route('profile.password.edit') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-lock me-1"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection