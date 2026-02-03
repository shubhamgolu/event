<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
      
    }

    // Show check-in page for an event
//     public function show(Event $event)
//     {

//          if (!auth()->user()->isAdmin()) {
//     abort(403, 'Admin access required');
// }
//         $participants = $event->participants()
//                              ->with('user')
//                              ->orderBy('checked_in')
//                              ->orderBy('created_at', 'desc')
//                              ->paginate(20);
        
//         return view('admin.checkin.show', compact('event', 'participants'));
//     }
public function show(Event $event)
{
    // Admin check
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Admin access required');
    }
    
    $participants = $event->participants()
                         ->with('user')
                         ->orderBy('checked_in')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);
    
    // Use simple view
    return view('admin.checkin.simple', compact('event', 'participants'));
}
    // Check-in a participant
    public function checkin(Participant $participant)
    {
        $participant->checkIn();
        
        return redirect()->back()
            ->with('success', "{$participant->user->name} checked in successfully!");
    }

    // Check-out a participant
    public function checkout(Participant $participant)
    {
        $participant->checkOut();
        
        return redirect()->back()
            ->with('success', "{$participant->user->name} checked out successfully!");
    }

    // Bulk check-in (QR code scanning)
    public function bulkCheckin(Request $request, Event $event)
    {
        $request->validate([
            'registration_numbers' => 'required|string',
        ]);
        
        $numbers = array_map('trim', explode("\n", $request->registration_numbers));
        $checkedIn = 0;
        $notFound = [];
        
        foreach ($numbers as $number) {
            $participant = Participant::where('registration_number', $number)
                                     ->where('event_id', $event->id)
                                     ->first();
            
            if ($participant && !$participant->checked_in) {
                $participant->checkIn();
                $checkedIn++;
            } elseif ($participant && $participant->checked_in) {
                // Already checked in
            } else {
                $notFound[] = $number;
            }
        }
        
        $message = "Checked in {$checkedIn} participants.";
        if (!empty($notFound)) {
            $message .= " Not found: " . implode(', ', $notFound);
        }
        
        return redirect()->back()
            ->with('success', $message);
    }

    // Search participants
    public function search(Request $request, Event $event)
    {
        $search = $request->get('search');
        
        $participants = Participant::where('event_id', $event->id)
                                  ->whereHas('user', function($query) use ($search) {
                                      $query->where('name', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%");
                                  })
                                  ->orWhere('registration_number', 'like', "%{$search}%")
                                  ->with('user')
                                  ->paginate(20);
        
        return view('admin.checkin.show', compact('event', 'participants', 'search'));
    }

    // Generate QR codes for event
    
    public function sendSurvey(Participant $participant)
{
    // Admin check
    if (!Auth::user()->isAdmin()) {
        abort(403, 'Admin access required');
    }
    
    // Check if already sent
    if ($participant->survey_sent) {
        return redirect()->back()
            ->with('info', 'Survey already sent to this participant.');
    }
    
    // Send survey
    $participant->sendSurvey();
    
    return redirect()->back()
        ->with('success', "Survey sent to {$participant->user->name}!");
}

}