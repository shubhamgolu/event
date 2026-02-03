@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Events</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.events.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Create New Event
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Events Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Registrations</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                <tr>
                                    <td>{{ $event->id }}</td>
                                    <td>
                                        <strong>{{ $event->name }}</strong>
                                        @if($event->description)
                                        <br><small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $event->formatted_date }}
                                        @if($event->time)
                                        <br><small>{{ $event->formatted_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($event->type == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @else
                                            <span class="badge bg-warning">Offline</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $event->participants_count }} / {{ $event->capacity ?? '∞' }}
                                        </span>
                                        @if($event->is_full)
                                            <span class="badge bg-danger">Full</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($event->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.events.show', $event) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.events.edit', $event) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.events.toggle-status', $event) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary" title="Toggle Status">
                                                    @if($event->is_active)
                                                        <i class="fas fa-toggle-on"></i>
                                                    @else
                                                        <i class="fas fa-toggle-off"></i>
                                                    @endif
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.events.destroy', $event) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this event?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-3"></i>
                                            <h5>No events found</h5>
                                            <p>Create your first event to get started</p>
                                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                                Create Event
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($events->hasPages())
                    <div class="mt-3">
                        {{ $events->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Events</h6>
                            <h3 class="mb-0">{{ $events->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Active Events</h6>
                            <h3 class="mb-0">{{ $events->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Online Events</h6>
                            <h3 class="mb-0">{{ $events->where('type', 'online')->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Registrations</h6>
                            <h3 class="mb-0">{{ $events->sum('participants_count') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection