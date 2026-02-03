@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center py-5">
                    <h1 class="display-5 fw-bold mb-3">Upcoming Events</h1>
                    <p class="lead mb-0">Discover and register for exciting events</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row g-4">
        @forelse($events as $event)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <!-- Event Status Badge -->
                <div class="position-absolute top-0 end-0 m-3">
                    @if(in_array($event->id, $registeredEvents))
                        <span class="badge bg-success">Registered</span>
                    @elseif($event->is_full)
                        <span class="badge bg-danger">Full</span>
                    @elseif($event->date < now()->format('Y-m-d'))
                        <span class="badge bg-secondary">Past Event</span>
                    @else
                        <span class="badge bg-primary">Open</span>
                    @endif
                </div>

                <!-- Event Image/Icon -->
                <div class="text-center py-4 bg-light">
                    @if($event->type == 'online')
                        <i class="fas fa-globe fa-3x text-primary"></i>
                    @else
                        <i class="fas fa-building fa-3x text-warning"></i>
                    @endif
                </div>

                <!-- Event Details -->
                <div class="card-body">
                    <h5 class="card-title">{{ $event->name }}</h5>
                    <p class="card-text text-muted small">
                        {{ Str::limit($event->description, 100) }}
                    </p>
                    
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-1">
                            <i class="fas fa-calendar-day text-primary me-2"></i>
                            {{ $event->formatted_date }}
                        </li>
                        @if($event->time)
                        <li class="mb-1">
                            <i class="fas fa-clock text-primary me-2"></i>
                            {{ $event->formatted_time }}
                        </li>
                        @endif
                        <li class="mb-1">
                            <i class="fas fa-users text-primary me-2"></i>
                            {{ $event->participants_count }} registered
                        </li>
                        <li class="mb-1">
                            <i class="fas fa-tag text-primary me-2"></i>
                            @if($event->price > 0)
                                ${{ number_format($event->price, 2) }}
                            @else
                                Free
                            @endif
                        </li>
                    </ul>
                </div>

                <!-- Card Footer -->
                <div class="card-footer bg-white border-0 pt-0">
                    <div class="d-grid gap-2">
                        @if(in_array($event->id, $registeredEvents))
                            <a href="{{ route('user.events.show', $event) }}" 
                               class="btn btn-outline-success">
                                <i class="fas fa-check-circle me-2"></i>View Registration
                            </a>
                        @elseif($event->is_full)
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-ban me-2"></i>Event Full
                            </button>
                        @elseif($event->date < now()->format('Y-m-d'))
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-history me-2"></i>Event Ended
                            </button>
                        @else
                            <a href="{{ route('user.events.show', $event) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h4>No Events Available</h4>
                    <p class="text-muted">Check back later for upcoming events.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
    <div class="row mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $events->links() }}
            </div>
        </div>
    </div>
    @endif

    <!-- My Events Button -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h5 class="card-title">Manage Your Registrations</h5>
                    <p class="card-text">View and manage all your event registrations in one place.</p>
                    <a href="{{ route('user.events.my-events') }}" class="btn btn-success">
                        <i class="fas fa-calendar-check me-2"></i>My Registered Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection