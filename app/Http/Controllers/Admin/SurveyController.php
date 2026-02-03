<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Manual admin check
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Admin access required');
            }
            return $next($request);
        });
    }

    // List all surveys
    public function index()
    {
        $surveys = Survey::with('event')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
        
        return view('admin.surveys.index', compact('surveys'));
    }

    // Show create survey form
    public function create()
    {
        // $events = Event::where('is_active', true)
        //               ->whereDoesntHave('survey') // Events without surveys
        //               ->get('event');
        $events = Event::where('is_active', true)
                  ->orderBy('date', 'asc')
                  ->get();
        //  print_r($events);
        //  exit;
        return view('admin.surveys.create', compact('events'));
    }

    // Create survey for specific event
    public function createForEvent(Event $event)
    {
        // Check if event already has survey
        if ($event->survey) {
            return redirect()->route('admin.surveys.edit', $event->survey)
                ->with('info', 'This event already has a survey. Editing existing survey.');
        }
        
        return view('admin.surveys.create', compact('event'));
    }

    // Store new survey
    // Store new survey - UPDATED FOR SIMPLE FORM
public function store(Request $request)
{
    \Log::info('====== SURVEY FORM SUBMISSION ======');
    \Log::info('Request Data:', $request->all());
    
    // Validate basic fields
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'title' => 'required|string|max:255',
    ]);
    
    try {
        // Build questions array from form data
        $questions = [];
        
        if ($request->has('questions') && is_array($request->questions)) {
            foreach ($request->questions as $index => $questionData) {
                if (!empty($questionData['question'])) {
                    $question = [
                        'type' => $questionData['type'] ?? 'multiple_choice',
                        'question' => trim($questionData['question']),
                        'required' => isset($questionData['required']) ? true : false,
                        'options' => []
                    ];
                    
                    // Add options if multiple choice
                    if ($question['type'] === 'multiple_choice' && isset($questionData['options'])) {
                        foreach ($questionData['options'] as $option) {
                            if (!empty(trim($option))) {
                                $question['options'][] = trim($option);
                            }
                        }
                    }
                    
                    // If multiple choice has no valid options, add defaults
                    if ($question['type'] === 'multiple_choice' && empty($question['options'])) {
                        $question['options'] = ['Excellent', 'Good', 'Average', 'Poor'];
                    }
                    
                    $questions[] = $question;
                }
            }
        }
        
        \Log::info('Processed questions:', $questions);
        
        // If no questions, add a default
        if (empty($questions)) {
            $questions[] = [
                'type' => 'multiple_choice',
                'question' => 'How would you rate the event?',
                'required' => true,
                'options' => ['Excellent', 'Good', 'Average', 'Poor']
            ];
        }
        
        // Delete existing survey for this event
        Survey::where('event_id', $request->event_id)->delete();
        
        // Create survey
        $survey = Survey::create([
            'event_id' => $request->event_id,
            'title' => $request->title,
            'description' => $request->description ?? '',
            'questions' => json_encode($questions),
            'send_on_checkin' => $request->has('send_on_checkin'),
            'send_on_checkout' => $request->has('send_on_checkout'),
            'is_active' => $request->has('is_active'),
        ]);
        
        \Log::info('Survey created:', ['id' => $survey->id, 'title' => $survey->title]);
        
        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey created successfully!');
            
    } catch (\Exception $e) {
        \Log::error('Survey creation error: ' . $e->getMessage());
        \Log::error('Trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error: ' . $e->getMessage());
    }
}

    // Show survey details
   // Show survey details
public function show(Survey $survey)
{
    // Load event with null check
    $survey->load(['event' => function($query) {
        $query->withTrashed(); // Include deleted events
    }]);
    
    // Check if event exists
    if (!$survey->event) {
        return redirect()->route('admin.surveys.index')
            ->with('error', 'The event associated with this survey no longer exists.');
    }
    
    return view('admin.surveys.show', compact('survey'));
}

    // Show edit form
    public function edit(Survey $survey)
    {
        $survey->load('event');
        return view('admin.surveys.edit', compact('survey'));
    }

    // Update survey
    public function update(Request $request, Survey $survey)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:multiple_choice,text',
            'questions.*.question' => 'required|string|max:500',
            'questions.*.required' => 'boolean',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice|array|min:2',
            'questions.*.options.*' => 'string|max:255',
            'send_on_checkin' => 'boolean',
            'send_on_checkout' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $survey->update([
            'title' => $request->title,
            'description' => $request->description,
            'questions' => json_encode($request->questions),
            'send_on_checkin' => $request->boolean('send_on_checkin'),
            'send_on_checkout' => $request->boolean('send_on_checkout'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'Survey updated successfully!');
    }

    // Delete survey
    public function destroy(Survey $survey)
    {
        $survey->delete();
        
        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully!');
    }

    // Preview survey
    public function preview(Survey $survey)
    {
        return view('admin.surveys.preview', compact('survey'));
    }

    // Clone survey to another event
    public function clone(Survey $survey)
    {
        $events = Event::where('is_active', true)
                      ->where('id', '!=', $survey->event_id)
                      ->whereDoesntHave('survey')
                      ->get();
        
        return view('admin.surveys.clone', compact('survey', 'events'));
    }

    // Store cloned survey
    public function storeClone(Request $request, Survey $survey)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id|unique:surveys,event_id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if event already has survey
        $existingSurvey = Survey::where('event_id', $request->event_id)->first();
        if ($existingSurvey) {
            return redirect()->route('admin.surveys.edit', $existingSurvey)
                ->with('info', 'Selected event already has a survey.');
        }

        $newSurvey = Survey::create([
            'event_id' => $request->event_id,
            'title' => $survey->title . ' (Copy)',
            'description' => $survey->description,
            'questions' => $survey->questions,
            'send_on_checkin' => $survey->send_on_checkin,
            'send_on_checkout' => $survey->send_on_checkout,
            'is_active' => false, // Inactive by default for cloned surveys
        ]);

        return redirect()->route('admin.surveys.edit', $newSurvey)
            ->with('success', 'Survey cloned successfully! Please review and activate.');
    }

    // Toggle survey status
    public function toggleStatus(Survey $survey)
    {
        $survey->update([
            'is_active' => !$survey->is_active
        ]);
        
        $status = $survey->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Survey {$status} successfully!");
    }
}