@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-check me-2"></i>
                    Check-in: {{ $event->name }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Event
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

            <!-- Quick Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Registered</h6>
                            <h3 class="mb-0">{{ $event->participants->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Checked In</h6>
                            <h3 class="mb-0">{{ $event->participants->where('checked_in', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Checked Out</h6>
                            <h3 class="mb-0">{{ $event->participants->where('checked_out', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Surveys Sent</h6>
                            <h3 class="mb-0">{{ $event->participants->where('survey_sent', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.checkin.search', $event) }}" method="GET">
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           name="search" placeholder="Search by name, email, or registration number..."
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" 
                                        data-bs-toggle="modal" data-bs-target="#bulkCheckinModal">
                                    <i class="fas fa-users me-1"></i> Bulk Check-in
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registration No.</th>
                                    <th>Check-in Status</th>
                                    <th>Survey Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($participants as $participant)
                                <tr class="{{ $participant->checked_in ? 'table-success' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $participant->user->name }}</strong>
                                    </td>
                                    <td>{{ $participant->user->email }}</td>
                                    <td>
                                        <code>{{ $participant->registration_number }}</code>
                                    </td>
                                    <td>
                                        @if($participant->checked_in)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Checked In
                                                @if($participant->checked_in_at)
                                                    <br>
                                                    <small>{{ $participant->checked_in_at->format('h:i A') }}</small>
                                                @endif
                                            </span>
                                            @if($participant->checked_out)
                                                <span class="badge bg-warning mt-1">
                                                    <i class="fas fa-sign-out-alt me-1"></i>
                                                    Checked Out
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-clock me-1"></i>
                                                Not Checked In
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($participant->survey_completed)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Completed
                                            </span>
                                        @elseif($participant->survey_sent)
                                            <span class="badge bg-info">Survey Sent</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$participant->checked_in)
                                                <form action="{{ route('admin.checkin.checkin', $participant) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" 
                                                            title="Check In">
                                                        <i class="fas fa-sign-in-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($participant->checked_in && !$participant->checked_out)
                                                <form action="{{ route('admin.checkin.checkout', $participant) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning" 
                                                            title="Check Out">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($participant->checked_in && !$participant->survey_sent)
                                                <form action="{{ route('admin.checkin.send-survey', $participant) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-info" 
                                                            title="Send Survey">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-2x mb-3"></i>
                                            <h5>No Participants</h5>
                                            <p>No one has registered for this event yet.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($participants->hasPages())
                    <div class="mt-3">
                        {{ $participants->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bulk Check-in Modal -->
<div class="modal fade" id="bulkCheckinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Check-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.checkin.bulk', $event) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Enter registration numbers (one per line):</p>
                    <textarea class="form-control" name="registration_numbers" 
                              rows="10" placeholder="REG-ABC123&#10;REG-DEF456"></textarea>
                    <div class="form-text mt-2">
                        Scan QR codes or copy-paste registration numbers
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Check-in</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection