@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">{{ $survey->title }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Surveys
                    </a>
                </div>
            </div>

            <!-- Survey Details -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Survey Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="30%">Title:</th>
                                    <td>{{ $survey->title }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $survey->description ?: 'No description' }}</td>
                                </tr>
                                <tr>
    <th>Event:</th>
    <td>
        @if($survey->event)
            <a href="{{ route('admin.events.show', $survey->event) }}">
                {{ $survey->event->name }}
            </a>
        @else
            <span class="text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>Event not found
            </span>
        @endif
    </td>
</tr>
                                <tr>
                                    <th>Questions:</th>
                                    <td>{{ $survey->question_count }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($survey->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Send On:</th>
                                    <td>
                                        @if($survey->send_on_checkin)
                                            <span class="badge bg-info mb-1">Check-in</span>
                                        @endif
                                        @if($survey->send_on_checkout)
                                            <span class="badge bg-warning">Check-out</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $survey->created_at->format('F d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $survey->updated_at->format('F d, Y h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Questions Preview -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Questions Preview</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $questions = json_decode($survey->questions, true);
                            @endphp
                            
                            @if($questions && count($questions) > 0)
                                @foreach($questions as $index => $question)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>Question {{ $index + 1 }}</strong>
                                        @if($question['required'])
                                            <span class="badge bg-danger float-end">Required</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-3">{{ $question['question'] }}</p>
                                        
                                        @if($question['type'] == 'multiple_choice')
                                            <div class="ms-3">
                                                @foreach($question['options'] as $option)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="radio" 
                                                           name="preview_{{ $index }}" id="preview_{{ $index }}_{{ $loop->index }}" disabled>
                                                    <label class="form-check-label" for="preview_{{ $index }}_{{ $loop->index }}">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <textarea class="form-control" rows="2" placeholder="Text answer..." disabled></textarea>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-question-circle fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No questions in this survey.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Survey
                                </a>
                               
                                <a href="{{ route('admin.surveys.clone', $survey) }}" class="btn btn-primary">
                                    <i class="fas fa-copy me-2"></i>Clone Survey
                                </a>
                                <form action="{{ route('admin.surveys.toggle-status', $survey) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        @if($survey->is_active)
                                            <i class="fas fa-toggle-on me-2"></i>Deactivate
                                        @else
                                            <i class="fas fa-toggle-off me-2"></i>Activate
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" 
                                      onsubmit="return confirm('Delete this survey?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Delete Survey
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Event Information</h5>
                        </div>
                        <div class="card-body">
                            <h6>{{ $survey->event->name }}</h6>
                            <p class="mb-2">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $survey->event->formatted_date }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-clock me-2"></i>
                                {{ $survey->event->formatted_time ?: 'Time TBD' }}
                            </p>
                            <p class="mb-0">
                                @if($survey->event->type == 'online')
                                    <i class="fas fa-globe me-2"></i>Online Event
                                @else
                                    <i class="fas fa-building me-2"></i>{{ $survey->event->location }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection