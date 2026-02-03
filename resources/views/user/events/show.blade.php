@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('user.events.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>
    </div>

    <!-- Event Details -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Event Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="card-title h2 mb-2">{{ $event->name }}</h1>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @if($event->type == 'online')
                                    <span class="badge bg-info">
                                        <i class="fas fa-globe me-1"></i>Online Event
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-building me-1"></i>Offline Event
                                    </span>
                                @endif
                                
                                @if($event->is_full)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-users me-1"></i>Event Full
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $event->available_spots }} spots left
                                    </span>
                                @endif
                                
                                @if($event->price > 0)
                                    <span class="badge bg-primary">
                                        <i class="fas fa-tag me-1"></i>${{ number_format($event->price, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-tag me-1"></i>Free
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        @if($isRegistered)
                            <span class="badge bg-success fs-6 p-3">
                                <i class="fas fa-check-circle me-2"></i>Registered
                            </span>
                        @endif
                    </div>

                    <!-- Event Description -->
                    <div class="mb-4">
                        <h5 class="mb-3">About This Event</h5>
                        <p class="card-text">{{ $event->description }}</p>
                    </div>

                    <!-- Event Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-calendar-alt text-primary me-2"></i>Event Schedule</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Date:</strong> {{ $event->formatted_date }}
                                </li>
                                @if($event->time)
                                <li class="mb-2">
                                    <strong>Time:</strong> {{ $event->formatted_time }}
                                </li>
                                @endif
                                <li class="mb-2">
                                    <strong>Duration:</strong> To be announced
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i>Event Location</h5>
                            <ul class="list-unstyled">
                                @if($event->type == 'online')
                                    <li class="mb-2">
                                        <strong>Meeting Link:</strong>
                                        @if($isRegistered)
                                            <a href="{{ $event->meeting_link }}" target="_blank" class="text-decoration-none">
                                                {{ $event->meeting_link }}
                                            </a>
                                        @else
                                            <span class="text-muted">Available after registration</span>
                                        @endif
                                    </li>
                                @else
                                    <li class="mb-2">
                                        <strong>Venue:</strong> {{ $event->location }}
                                    </li>
                                @endif
                                <li class="mb-2">
                                    <strong>Capacity:</strong> 
                                    {{ $event->capacity ? $event->capacity . ' participants' : 'Unlimited' }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Registration Status -->
                    @if($isRegistered && $participant)
                    <div class="alert alert-success">
                        <h5><i class="fas fa-check-circle me-2"></i>Registration Confirmed</h5>
                        <p class="mb-2">
                            <strong>Registration Number:</strong> {{ $participant->registration_number }}
                        </p>
                        <p class="mb-2">
                            <strong>Registered On:</strong> {{ $participant->created_at->format('F d, Y h:i A') }}
                        </p>
                        @if($participant->checked_in)
                            <p class="mb-2">
                                <strong>Checked In:</strong> {{ $participant->checked_in_at->format('F d, Y h:i A') }}
                            </p>
                        @endif
                        
                        <!-- Cancel Registration Button -->
                        @if(!$participant->checked_in)
                        <form action="{{ route('user.events.cancel-registration', $event) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel your registration?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times me-2"></i>Cancel Registration
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Registration/Details -->
        <div class="col-lg-4">
            <!-- Registration Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Registration
                    </h5>
                </div>
                <div class="card-body">
                    @if($isRegistered)
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>You're Registered!</h5>
                            <p class="text-muted">We'll send you updates about this event.</p>
                            <a href="{{ route('user.events.my-events') }}" class="btn btn-success">
                                <i class="fas fa-calendar-check me-2"></i>View My Events
                            </a>
                        </div>
                    @elseif($event->is_full)
                        <div class="text-center py-3">
                            <i class="fas fa-ban fa-3x text-danger mb-3"></i>
                            <h5>Event Full</h5>
                            <p class="text-muted">This event has reached maximum capacity.</p>
                            <button class="btn btn-secondary" disabled>
                                Registration Closed
                            </button>
                        </div>
                    @elseif($event->date < now()->format('Y-m-d'))
                        <div class="text-center py-3">
                            <i class="fas fa-history fa-3x text-secondary mb-3"></i>
                            <h5>Event Ended</h5>
                            <p class="text-muted">This event has already occurred.</p>
                            <button class="btn btn-secondary" disabled>
                                Registration Closed
                            </button>
                        </div>
                    @else
                        <form action="{{ route('user.events.register', $event) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="additional_info" class="form-label">Additional Information (Optional)</label>
                                <textarea class="form-control" id="additional_info" name="additional_info" 
                                          rows="3" placeholder="Any special requirements or comments..."></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Register Now
                                </button>
                            </div>
                            
                            <div class="mt-3 text-center">
                                @if($event->price > 0)
                                    <p class="text-muted small">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        This event requires payment of ${{ number_format($event->price, 2) }}
                                    </p>
                                @else
                                    <p class="text-success small">
                                        <i class="fas fa-check-circle me-1"></i>
                                        This is a free event
                                    </p>
                                @endif
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Event Organizer -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>Event Organizer
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <img src="https://ui-avatars.com/api/?name={{ $event->user->name }}&background=007bff&color=fff&size=48" 
                                 class="rounded-circle" alt="Organizer">
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $event->user->name }}</h6>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-envelope me-1"></i>{{ $event->user->email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection