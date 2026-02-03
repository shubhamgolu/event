@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Survey: {{ $survey->title }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> View Survey
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

            <!-- Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.surveys.update', $survey) }}" method="POST" id="survey-form">
                        @csrf
                        @method('PUT')

                        <!-- Event Info (Read-only) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3"><i class="fas fa-calendar me-2"></i>Event Information</h5>
                                <div class="alert alert-info">
                                    <strong>Event:</strong> {{ $survey->event->name }}<br>
                                    <strong>Date:</strong> {{ $survey->event->formatted_date }}
                                </div>
                            </div>
                        </div>

                        <!-- Survey Details -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Survey Details</h5>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Survey Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $survey->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $survey->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="mb-3"><i class="fas fa-cog me-2"></i>Settings</h5>
                                
                                <div class="mb-3">
                                    <label class="form-label">When to send survey?</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="send_on_checkin" 
                                               id="send_on_checkin" value="1" {{ old('send_on_checkin', $survey->send_on_checkin) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="send_on_checkin">
                                            <i class="fas fa-sign-in-alt me-1"></i> On Check-in
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="send_on_checkout" 
                                               id="send_on_checkout" value="1" {{ old('send_on_checkout', $survey->send_on_checkout) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="send_on_checkout">
                                            <i class="fas fa-sign-out-alt me-1"></i> On Check-out
                                        </label>
                                    </div>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                           id="is_active" name="is_active" value="1" {{ old('is_active', $survey->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Survey
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Builder -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">
                                    <i class="fas fa-question-circle me-2"></i>Survey Questions
                                    <button type="button" class="btn btn-sm btn-success float-end" onclick="addQuestion()">
                                        <i class="fas fa-plus me-1"></i> Add Question
                                    </button>
                                </h5>
                                
                                <div id="questions-container">
                                    <!-- Questions will be added here dynamically -->
                                </div>
                                
                                <div class="text-center mt-3" id="no-questions-message">
                                    <p class="text-muted">No questions added yet. Click "Add Question" to start.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field for questions -->
                        <input type="hidden" name="questions" id="questions-data" value="{{ old('questions', $survey->questions) }}">

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> Update Survey
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Include the same JavaScript as create.blade.php -->
<!-- Copy the entire script section from create.blade.php here -->

<script>
// Copy the entire JavaScript from create.blade.php here
// (Same question builder functionality)
</script>
@endsection