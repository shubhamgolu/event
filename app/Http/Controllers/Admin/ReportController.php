<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participant;
use App\Models\SurveyResponse;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Admin access required');
            }
            return $next($request);
        });
    }

    /**
     * Show main reports dashboard
     */
    public function index()
    {
        // Get overall statistics
        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('is_active', true)->count(),
            'total_participants' => Participant::count(),
            'checked_in_participants' => Participant::where('checked_in', true)->count(),
            'survey_responses' => SurveyResponse::count(),
            'certificates_generated' => Certificate::count(),
        ];

        // Get recent events with stats
        $recentEvents = Event::withCount(['participants', 'certificates'])
            ->with(['survey'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($event) {
                // Manually calculate survey responses count
                $event->survey_responses_count = $event->survey ? $event->survey->responses()->count() : 0;
                return $event;
            });

        return view('admin.reports.index', compact('stats', 'recentEvents'));
    }

    /**
     * Event Registration Report
     */
    public function eventRegistrations(Request $request)
    {
        $query = Event::withCount(['participants', 'certificates'])
            ->with(['survey']);
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        
        // Filter by event type
        if ($request->has('event_type')) {
            $query->where('type', $request->event_type);
        }
        
        $events = $query->orderBy('date', 'desc')->paginate(20);
        
        // Add survey responses count manually
        foreach ($events as $event) {
            $event->survey_responses_count = $event->survey ? $event->survey->responses()->count() : 0;
        }
        
        $eventTypes = Event::select('type')->distinct()->pluck('type');
        
        return view('admin.reports.event-registrations', compact('events', 'eventTypes'));
    }

    /**
     * Survey Responses Report
     */
    public function surveyResponses(Request $request)
    {
        $query = SurveyResponse::with(['participant.user', 'survey.event']);
        
        // Filter by date
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('submitted_at', [$startDate, $endDate]);
        }
        
        // Filter by event
        if ($request->has('event_id')) {
            $query->whereHas('survey', function($q) use ($request) {
                $q->where('event_id', $request->event_id);
            });
        }
        
        $responses = $query->orderBy('submitted_at', 'desc')->paginate(20);
        
        $events = Event::has('survey')->with('survey')->get();
        
        return view('admin.reports.survey-responses', compact('responses', 'events'));
    }

    /**
     * Certificate Report
     */
    public function certificates(Request $request)
    {
        $query = Certificate::with(['participant.user', 'event']);
        
        // Filter by date
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('generated_at', [$startDate, $endDate]);
        }
        
        // Filter by event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        $certificates = $query->orderBy('generated_at', 'desc')->paginate(20);
        
        $events = Event::has('certificates')->get();
        
        return view('admin.reports.certificates', compact('certificates', 'events'));
    }

    /**
     * Detailed Event Report
     */
    public function eventDetail($eventId)
    {
        $event = Event::withCount(['participants', 'certificates'])
            ->with(['participants.user', 'survey'])
            ->findOrFail($eventId);
        
        // Get participation stats
        $participationStats = [
            'total_registered' => $event->participants_count,
            'checked_in' => $event->participants->where('checked_in', true)->count(),
            'checked_out' => $event->participants->where('checked_out', true)->count(),
            'survey_sent' => $event->participants->where('survey_sent', true)->count(),
            'survey_completed' => $event->participants->where('survey_completed', true)->count(),
            'certificates_sent' => $event->participants->where('certificate_sent', true)->count(),
        ];
        
        // Get survey response data if survey exists
        $surveyData = [];
        if ($event->survey) {
            $responses = $event->survey->responses;
            
            // Analyze responses
            $surveyData = [
                'total_responses' => $responses->count(),
                'response_rate' => $participationStats['total_registered'] > 0 ? 
                    round(($responses->count() / $participationStats['total_registered']) * 100, 2) : 0,
            ];
        }
        
        return view('admin.reports.event-detail', compact('event', 'participationStats', 'surveyData'));
    }

    /**
     * Export Event Registrations to CSV
     */
    public function exportEventRegistrations(Request $request)
    {
        $events = Event::with(['survey'])
            ->withCount(['participants', 'certificates'])
            ->orderBy('date', 'desc')
            ->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="event_registrations_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Event ID', 'Event Name', 'Date', 'Type', 'Registrations', 'Survey Responses', 'Certificates', 'Response Rate']);
            
            // Add data rows
            foreach ($events as $event) {
                $surveyResponsesCount = $event->survey ? $event->survey->responses()->count() : 0;
                $responseRate = $event->participants_count > 0 ? 
                    round(($surveyResponsesCount / $event->participants_count) * 100, 2) : 0;
                
                fputcsv($file, [
                    $event->id,
                    $event->name,
                    $event->date->format('Y-m-d'),
                    $event->type,
                    $event->participants_count,
                    $surveyResponsesCount,
                    $event->certificates_count,
                    $responseRate . '%'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}