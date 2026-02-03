@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Surveys</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Create New Survey
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
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Surveys Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Survey Title</th>
                                    <th>Event</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Trigger</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($surveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>
                                        <strong>{{ $survey->title }}</strong>
                                        @if($survey->description)
                                        <br><small class="text-muted">{{ Str::limit($survey->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($survey->event)
                                            <a href="{{ route('admin.events.show', $survey->event) }}" 
                                               class="text-decoration-none">
                                                {{ $survey->event->name }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $survey->event->formatted_date }}</small>
                                        @else
                                            <span class="text-muted">No event assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $survey->questionCount }}</span>

                                    </td>
                                    <td>
                                        @if($survey->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($survey->send_on_checkin)
                                            <span class="badge bg-info mb-1">On Check-in</span>
                                        @endif
                                        @if($survey->send_on_checkout)
                                            <span class="badge bg-warning">On Check-out</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.surveys.show', $survey) }}" 
                                               class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.surveys.preview', $survey) }}" 
                                               class="btn btn-secondary" title="Preview" target="_blank">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            <a href="{{ route('admin.surveys.edit', $survey) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.surveys.clone', $survey) }}" 
                                               class="btn btn-primary" title="Clone">
                                                <i class="fas fa-copy"></i>
                                            </a>
                                            <form action="{{ route('admin.surveys.toggle-status', $survey) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-secondary" title="Toggle Status">
                                                    @if($survey->is_active)
                                                        <i class="fas fa-toggle-on"></i>
                                                    @else
                                                        <i class="fas fa-toggle-off"></i>
                                                    @endif
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.surveys.destroy', $survey) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this survey?')">
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
                                            <i class="fas fa-poll fa-2x mb-3"></i>
                                            <h5>No surveys found</h5>
                                            <p>Create your first survey to get started</p>
                                            <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">
                                                Create Survey
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($surveys->hasPages())
                    <div class="mt-3">
                        {{ $surveys->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">Total Surveys</h6>
                            <h3 class="mb-0">{{ $surveys->total() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">Active Surveys</h6>
                            <h3 class="mb-0">{{ $surveys->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">Check-in Surveys</h6>
                            <h3 class="mb-0">{{ $surveys->where('send_on_checkin', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">Check-out Surveys</h6>
                            <h3 class="mb-0">{{ $surveys->where('send_on_checkout', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection