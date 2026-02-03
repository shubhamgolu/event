@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h2 mb-2">My Registered Events</h1>
                            <p class="mb-0">Manage all your event registrations</p>
                        </div>
                        <a href="{{ route('user.events.index') }}" class="btn btn-light">
                            <i class="fas fa-calendar-plus me-2"></i>Browse More Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    @if($events->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check-in</th>
                                    <th>Survey</th>
                                    <th>Certificate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                @php
                                    $participant = Auth::user()->getParticipantForEvent($event->id);
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $event->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $event->type == 'online' ? 'Online' : 'Offline' }}</small>
                                    </td>
                                    <td>
                                        {{ $event->formatted_date }}
                                        @if($event->time)
                                        <br>
                                        <small>{{ $event->formatted_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($event->date < now()->format('Y-m-d'))
                                            <span class="badge bg-secondary">Completed</span>
                                        @else
                                            <span class="badge bg-success">Upcoming</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($participant->checked_in)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Checked In
                                            </span>
                                            <br>
                                            <small>{{ $participant->checked_in_at->format('M d, h:i A') }}</small>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($participant->survey_completed)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Completed
                                            </span>
                                        @elseif($participant->survey_sent)
                                            <span class="badge bg-info">Sent</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($participant->certificate_sent)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Sent
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('user.events.show', $event) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$participant->checked_in)
                                            <form action="{{ route('user.events.cancel-registration', $event) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Cancel registration?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
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
        </div>
    </div>
    @else
    <!-- No Events -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h4>No Registrations Yet</h4>
                    <p class="text-muted mb-4">You haven't registered for any events yet.</p>
                    <a href="{{ route('user.events.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus me-2"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection