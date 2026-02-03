@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">{{ $event->name }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Events
                    </a>
                </div>
            </div>

            <!-- Event Details -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Event Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="30%">Event Name:</th>
                                    <td>{{ $event->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $event->description }}</td>
                                </tr>
                                <tr>
                                    <th>Date & Time:</th>
                                    <td>
                                        {{ $event->formatted_date }}
                                        @if($event->time)
                                        at {{ $event->formatted_time }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        @if($event->type == 'online')
                                            <span class="badge bg-info">Online</span>
                                        @else
                                            <span class="badge bg-warning">Offline</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Location/Link:</th>
                                    <td>
                                        @if($event->type == 'online')
                                            <a href="{{ $event->meeting_link }}" target="_blank">{{ $event->meeting_link }}</a>
                                        @else
                                            {{ $event->location }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td>
                                        @if($event->price > 0)
                                            ${{ number_format($event->price, 2) }}
                                        @else
                                            Free
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Capacity:</th>
                                    <td>
                                        {{ $event->capacity ?? 'Unlimited' }}
                                        ({{ $event->participants->count() }} registered)
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($event->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $event->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Created At:</th>
                                    <td>{{ $event->created_at->format('F d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Event
                                </a>
                                <a href="{{ route('admin.checkin.show', $event) }}" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-user-check me-2"></i>Check-in Participants
                                    </a>
                                <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary w-100">
                                        @if($event->is_active)
                                            <i class="fas fa-toggle-on me-2"></i>Deactivate
                                        @else
                                            <i class="fas fa-toggle-off me-2"></i>Activate
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST" 
                                      onsubmit="return confirm('Delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Delete Event
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Registered Participants ({{ $participants->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($participants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registration No.</th>
                                    <th>Check-in Status</th>
                                    <th>Registered On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($participants as $participant)
                                <tr>
                                    <td>{{ $participant->id }}</td>
                                    <td>{{ $participant->user->name }}</td>
                                    <td>{{ $participant->user->email }}</td>
                                    <td>{{ $participant->registration_number }}</td>
                                    <td>
                                        @if($participant->checked_in)
                                            <span class="badge bg-success">Checked In</span>
                                        @else
                                            <span class="badge bg-warning">Not Checked In</span>
                                        @endif
                                    </td>
                                    <td>{{ $participant->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $participants->links() }}
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No participants registered yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection