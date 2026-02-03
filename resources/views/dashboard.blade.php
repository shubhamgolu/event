@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div class="container text-center">
        <h1 class="display-4">Welcome, {{ Auth::user()->name }}!</h1>
        <p class="lead">Event Management System</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Events</h5>
                    <p class="card-text">Browse and register for upcoming events</p>
                    <a href="#" class="btn btn-primary">View Events</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-check fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Surveys</h5>
                    <p class="card-text">Complete surveys for events you attended</p>
                    <a href="#" class="btn btn-success">My Surveys</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-certificate fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Certificates</h5>
                    <p class="card-text">Download your event participation certificates</p>
                    <a href="#" class="btn btn-warning">My Certificates</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Panel (Visible only to admin) -->
   @if(Auth::user()->isAdmin())
<div class="row mt-5">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-crown me-2"></i>Admin Panel Access</h4>
            </div>
            <div class="card-body">
                <p>You have administrator privileges.</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger">
                    <i class="fas fa-sign-in-alt me-2"></i>Go to Admin Panel
                </a>
            </div>
        </div>
    </div>
</div>
@endif
    
    <!-- User Events Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Recent Activity</h4>
                </div>
                <div class="card-body">
                    <p>No recent activity. Register for an event to get started!</p>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection