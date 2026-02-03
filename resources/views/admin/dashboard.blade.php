@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Admin Dashboard</h1>
                    <div>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i>Create Event
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Event</h6>
                                        <h3 class="mb-0">{{ \App\Models\Event::count() }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Active Events</h6>
                                        <h3 class="mb-0">{{ \App\Models\Event::where('is_active', true)->count() }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-calendar-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Users</h6>
                                        <h3 class="mb-0">{{ \App\Models\User::count() }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Registrations</h6>
                                        <h3 class="mb-0">{{ \App\Models\Participant::count() }}</h3>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-user-plus fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('admin.events.create') }}" class="btn btn-outline-primary d-block p-3 text-center">
                                            <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                            Create Event
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-success d-block p-3 text-center">
                                            <i class="fas fa-list fa-2x mb-2 d-block"></i>
                                            Manage Events
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('user.events.index') }}" class="btn btn-outline-info d-block p-3 text-center">
                                            <i class="fas fa-eye fa-2x mb-2 d-block"></i>
                                            View User Portal
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-block p-3 text-center">
                                            <i class="fas fa-tachometer-alt fa-2x mb-2 d-block"></i>
                                            User Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Events -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Events</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $recentEvents = \App\Models\Event::latest()->take(5)->get();
                        @endphp
                        
                        @if($recentEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Registrations</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvents as $event)
                                    <tr>
                                        <td>{{ $event->name }}</td>
                                        <td>{{ $event->formatted_date }}</td>
                                        <td>
                                            @if($event->type == 'online')
                                                <span class="badge bg-info">Online</span>
                                            @else
                                                <span class="badge bg-warning">Offline</span>
                                            @endif
                                        </td>
                                        <td>{{ $event->participants->count() }}</td>
                                        <td>
                                            @if($event->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                            <p class="text-muted">No events created yet.</p>
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                Create First Event
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>