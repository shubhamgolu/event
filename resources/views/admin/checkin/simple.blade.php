@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Check-in: {{ $event->name }}</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Registration #</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $participant)
                    <tr>
                        <td>{{ $participant->user->name }}</td>
                        <td><code>{{ $participant->registration_number }}</code></td>
                        <td>
                            @if($participant->checked_in)
                                <span class="badge bg-success">Checked In</span>
                                @if($participant->checked_out)
                                    <span class="badge bg-warning">Checked Out</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Not Checked In</span>
                            @endif
                        </td>
                        <td>
                            @if(!$participant->checked_in)
                                <form action="{{ route('admin.checkin.checkin', $participant) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Check In</button>
                                </form>
                            @endif
                            
                            @if($participant->checked_in && !$participant->checked_out)
                                <form action="{{ route('admin.checkin.checkout', $participant) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Check Out</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection