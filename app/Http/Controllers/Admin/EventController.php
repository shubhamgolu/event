<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    // Temporarily comment out admin middleware
    // $this->middleware('admin');
    
    // Add manual admin check
    $this->middleware(function ($request, $next) {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Admin access required');
        }
        return $next($request);
    });
    }

    // List all events
    public function index()
    {
        $events = Event::withCount('participants')
                      ->orderBy('date', 'asc')
                      ->paginate(10);
        
        return view('admin.events.index', compact('events'));
    }

    // Show create event form
    public function create()
    {
        return view('admin.events.create');
    }

    // Store new event
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'type' => 'required|in:online,offline',
            'location' => 'required_if:type,offline|nullable|string|max:500',
            'meeting_link' => 'required_if:type,online|nullable|url|max:500',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'type' => $request->type,
            'location' => $request->location,
            'meeting_link' => $request->meeting_link,
            'price' => $request->price ?? 0,
            'capacity' => $request->capacity,
            'is_active' => $request->boolean('is_active'),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully!');
    }

    // Show single event
    public function show(Event $event)
    {
        $event->load(['participants.user', 'survey']);
        $participants = $event->participants()->with('user')->paginate(10);
        
        return view('admin.events.show', compact('event', 'participants'));
    }

    // Show edit form
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // Update event
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'type' => 'required|in:online,offline',
            'location' => 'required_if:type,offline|nullable|string|max:500',
            'meeting_link' => 'required_if:type,online|nullable|url|max:500',
            'price' => 'nullable|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $event->update([
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'type' => $request->type,
            'location' => $request->location,
            'meeting_link' => $request->meeting_link,
            'price' => $request->price ?? 0,
            'capacity' => $request->capacity,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully!');
    }

    // Delete event
    public function destroy(Event $event)
    {
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    // Toggle event active status
    public function toggleStatus(Event $event)
    {
        $event->update([
            'is_active' => !$event->is_active
        ]);
        
        $status = $event->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Event {$status} successfully!");
    }
}