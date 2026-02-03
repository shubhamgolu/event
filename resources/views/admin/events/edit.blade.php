@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Event: {{ $event->name }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> View Event
                    </a>
                </div>
            </div>

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.events.update', $event) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Event Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $event->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $event->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date" class="form-label">Date *</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                               id="date" name="date" value="{{ old('date', $event->date->format('Y-m-d')) }}" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="time" class="form-label">Time</label>
                                        <input type="time" class="form-control @error('time') is-invalid @enderror" 
                                               id="time" name="time" value="{{ old('time', $event->time ? $event->time->format('H:i') : '') }}">
                                        @error('time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Settings</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">Event Type *</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="type" id="online" 
                                               value="online" {{ old('type', $event->type) == 'online' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="online">
                                            <i class="fas fa-globe me-1"></i> Online Event
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="offline" 
                                               value="offline" {{ old('type', $event->type) == 'offline' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="offline">
                                            <i class="fas fa-building me-1"></i> Offline Event
                                        </label>
                                    </div>
                                    @error('type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="location-field" style="display: {{ $event->type == 'offline' ? 'block' : 'none' }}">
                                    <label for="location" class="form-label">Venue/Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="{{ old('location', $event->location) }}">
                                </div>

                                <div class="mb-3" id="meeting-link-field" style="display: {{ $event->type == 'online' ? 'block' : 'none' }}">
                                    <label for="meeting_link" class="form-label">Meeting Link</label>
                                    <input type="url" class="form-control" id="meeting_link" name="meeting_link" 
                                           value="{{ old('meeting_link', $event->meeting_link) }}" placeholder="https://">
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_active" name="is_active" value="1" {{ $event->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Event
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Capacity -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-tag me-2"></i>Pricing</h5>
                                
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $event->price) }}" min="0" step="0.01">
                                    <div class="form-text">Enter 0 for free events</div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="fas fa-users me-2"></i>Capacity</h5>
                                
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Maximum Participants</label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1">
                                    <div class="form-text">Leave empty for unlimited</div>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Update Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- JavaScript for dynamic fields -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const onlineRadio = document.getElementById('online');
    const offlineRadio = document.getElementById('offline');
    const locationField = document.getElementById('location-field');
    const meetingLinkField = document.getElementById('meeting-link-field');

    function toggleFields() {
        if (onlineRadio.checked) {
            meetingLinkField.style.display = 'block';
            locationField.style.display = 'none';
        } else {
            meetingLinkField.style.display = 'none';
            locationField.style.display = 'block';
        }
    }

    // Initial toggle
    toggleFields();

    // Add event listeners
    onlineRadio.addEventListener('change', toggleFields);
    offlineRadio.addEventListener('change', toggleFields);
});
</script>
@endsection