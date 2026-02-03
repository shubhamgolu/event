@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row align-items-center min-vh-100">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold text-primary">Event Management System</h1>
            <p class="lead mb-4">
                Register for events, complete surveys, and receive certificates automatically.
                Perfect for conferences, workshops, and online seminars.
            </p>
            <div class="d-grid gap-3 d-md-flex">
                @guest
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-user-plus me-2"></i>Get Started
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-5">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/3063/3063812.png" alt="Event System" class="img-fluid" style="max-height: 400px;">
        </div>
    </div>
    
    <div class="row mt-5 pt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-1 text-primary mb-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4>Easy Event Registration</h4>
                    <p>Browse and register for events with a single click</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-1 text-success mb-3">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h4>Automated Surveys</h4>
                    <p>Receive surveys automatically when you check-in/out</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="display-1 text-warning mb-3">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Instant Certificates</h4>
                    <p>Get certificates emailed immediately after survey completion</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection