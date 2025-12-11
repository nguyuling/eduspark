@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg text-center">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Welcome to EduSpark</h2>
                </div>

                <div class="card-body">
                    <h3 class="display-6 mb-4">Hello, {{ Auth::user()->name }}!</h3>
                    
                    <p class="lead">
                        You are logged in as a **{{ ucfirst(Auth::user()->role) }}**. 
                        Please use the module links in the **navigation bar at the top of the page** to continue.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection