<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // List all active events for users
    public function index()
    {
        $events = Event::where('is_active', true)
                      ->where('date', '>=', now()->format('Y-m-d'))
                      ->withCount('participants')
                      ->orderBy('date', 'asc')
                      ->paginate(9);
        
        $registeredEvents = Auth::user()->registeredEvents()->pluck('event_id')->toArray();
        
        return view('user.events.index', compact('events', 'registeredEvents'));
    }

    // Show single event details
    public function show(Event $event)
    {
        if (!$event->is_active) {
            abort(404, 'Event not found or inactive');
        }
        
        $isRegistered = Auth::user()->isRegisteredForEvent($event->id);
        $participant = $isRegistered ? Auth::user()->getParticipantForEvent($event->id) : null;
        
        return view('user.events.show', compact('event', 'isRegistered', 'participant'));
    }

    // Register for event
    public function register(Request $request, Event $event)
    {
        // Check if event is active
        if (!$event->is_active) {
            return redirect()->back()
                ->with('error', 'This event is no longer available for registration.');
        }

        // Check if event date has passed
        if ($event->date < now()->format('Y-m-d')) {
            return redirect()->back()
                ->with('error', 'This event has already occurred.');
        }

        // Check if event is full
        if ($event->is_full) {
            return redirect()->back()
                ->with('error', 'This event is full. No more registrations accepted.');
        }

        // Check if already registered
        if (Auth::user()->isRegisteredForEvent($event->id)) {
            return redirect()->back()
                ->with('info', 'You are already registered for this event.');
        }

        // Create registration
        $participant = Participant::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'registration_number' => Participant::generateRegistrationNumber(),
            'additional_info' => $request->additional_info,
        ]);

        return redirect()->route('user.events.show', $event)
            ->with('success', 'Successfully registered for ' . $event->name);
    }

    // Cancel registration
    public function cancelRegistration(Event $event)
    {
        $participant = Auth::user()->getParticipantForEvent($event->id);
        
        if (!$participant) {
            return redirect()->back()
                ->with('error', 'You are not registered for this event.');
        }

        // Cannot cancel if already checked in
        if ($participant->checked_in) {
            return redirect()->back()
                ->with('error', 'Cannot cancel registration after check-in.');
        }

        $participant->delete();

        return redirect()->route('user.events.show', $event)
            ->with('success', 'Registration cancelled successfully.');
    }

    // User's registered events
    public function myEvents()
    {
        $events = Auth::user()->registeredEvents()
                     ->orderByPivot('created_at', 'desc')
                     ->paginate(10);
        
        return view('user.events.my-events', compact('events'));
    }
}