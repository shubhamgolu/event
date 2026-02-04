<?php

namespace App\Http\Controllers;

use App\Models\SurveyToken;
use App\Models\Participant;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicSurveyController extends Controller
{
    public function show($token)
{
    try {
        Log::info("=== SURVEY DEBUG START ===");
        Log::info("Token: {$token}");
        
        // Check if token exists using DB facade first
        $surveyToken = DB::table('survey_tokens')
            ->where('token', $token)
            ->first();
        
        Log::info("Token found in DB: " . ($surveyToken ? 'YES' : 'NO'));
        
        if ($surveyToken) {
            Log::info("Token details:", (array)$surveyToken);
            Log::info("Token expired: " . ($surveyToken->expires_at < now() ? 'YES' : 'NO'));
            Log::info("Token used: " . ($surveyToken->used ? 'YES' : 'NO'));
        }
        
        // Now check with Eloquent (or use DB if SurveyToken model doesn't exist)
        $validToken = DB::table('survey_tokens')
            ->where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
        
        if (!$validToken) {
            Log::warning("Invalid or expired token: {$token}");
            return view('survey.error', [
                'title' => 'Invalid Survey Link',
                'message' => 'This survey link is invalid, expired, or has already been used.'
            ]);
        }
        
        // Get participant using DB joins
        $participantData = DB::table('participants')
            ->join('users', 'participants.user_id', '=', 'users.id')
            ->join('events', 'participants.event_id', '=', 'events.id')
            ->leftJoin('surveys', 'events.id', '=', 'surveys.event_id')
            ->where('participants.id', $validToken->participant_id)
            ->select(
                'participants.*',
                'users.name as user_name',
                'users.email as user_email',
                'events.name as event_name',
                'events.date as event_date',
                'surveys.id as survey_id',
                'surveys.title as survey_title',
                'surveys.questions as survey_questions'
            )
            ->first();
        
        if (!$participantData || !$participantData->survey_id) {
            Log::error("Participant or survey not found for token: {$token}");
            return view('survey.error', [
                'title' => 'Survey Not Found',
                'message' => 'The survey for this event is not available.'
            ]);
        }
        
        // Check if already submitted
        $existingResponse = DB::table('survey_responses')
            ->where('participant_id', $validToken->participant_id)
            ->where('survey_id', $participantData->survey_id)
            ->first();
        
        // Decode questions
        $questions = json_decode($participantData->survey_questions, true) ?? [];
        
        if ($existingResponse) {
            Log::info("Survey already submitted for participant: {$validToken->participant_id}");
            return view('survey.show', [
                'token' => $token,
                'participant' => (object) [
                    'id' => $participantData->id,
                    'user' => (object) ['name' => $participantData->user_name, 'email' => $participantData->user_email]
                ],
                'event' => (object) [
                    'name' => $participantData->event_name,
                    'date' => $participantData->event_date,
                    'survey' => (object) [
                        'id' => $participantData->survey_id,
                        'title' => $participantData->survey_title,
                        'questions' => $participantData->survey_questions
                    ]
                ],
                'survey' => (object) [
                    'id' => $participantData->survey_id,
                    'title' => $participantData->survey_title,
                    'questions' => $participantData->survey_questions
                ],
                'questions' => $questions,
                'pageTitle' => $participantData->survey_title,
                'alreadySubmitted' => true
            ]);
        }
        
        Log::info("Showing survey for participant: {$validToken->participant_id}, Event: {$participantData->event_name}");
        
        return view('survey.show', [
            'token' => $token,
            'participant' => (object) [
                'id' => $participantData->id,
                'user' => (object) ['name' => $participantData->user_name, 'email' => $participantData->user_email]
            ],
            'event' => (object) [
                'name' => $participantData->event_name,
                'date' => $participantData->event_date,
                'survey' => (object) [
                    'id' => $participantData->survey_id,
                    'title' => $participantData->survey_title,
                    'questions' => $participantData->survey_questions
                ]
            ],
            'survey' => (object) [
                'id' => $participantData->survey_id,
                'title' => $participantData->survey_title,
                'questions' => $participantData->survey_questions
            ],
            'questions' => $questions,
            'pageTitle' => $participantData->survey_title,
            'alreadySubmitted' => false
        ]);
        
    } catch (\Exception $e) {
        Log::error("Survey show error: " . $e->getMessage());
        Log::error("Trace: " . $e->getTraceAsString());
        
        return view('survey.error', [
            'title' => 'Error Loading Survey',
            'message' => 'An error occurred while loading the survey. Please try again.'
        ]);
    }
}
    
    public function submit(Request $request, $token)
    {
        DB::beginTransaction();
        
        try {
            $surveyToken = SurveyToken::where('token', $token)
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->first();
            
            if (!$surveyToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired survey link.'
                ], 400);
            }
            
            $participant = Participant::with(['event.survey'])->find($surveyToken->participant_id);
            
            if (!$participant || !$participant->event || !$participant->event->survey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Survey not found.'
                ], 404);
            }
            
            $survey = $participant->event->survey;
            
            // Check if already submitted
            $existingResponse = SurveyResponse::where('participant_id', $participant->id)
                ->where('survey_id', $survey->id)
                ->first();
            
            if ($existingResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Survey already submitted.'
                ], 400);
            }
            
            // Validate answers
            $questions = json_decode($survey->questions, true) ?? [];
            $answers = $request->input('answers', []);
            $validatedAnswers = [];
            
            foreach ($questions as $index => $question) {
                $questionKey = "question_{$index}";
                
                if (isset($question['required']) && $question['required'] && empty($answers[$questionKey])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Question '{$question['question']}' is required.",
                        'field' => $questionKey
                    ], 422);
                }
                
                if (!empty($answers[$questionKey])) {
                    $validatedAnswers[] = [
                        'question' => $question['question'],
                        'answer' => $answers[$questionKey],
                        'type' => $question['type'] ?? 'text'
                    ];
                }
            }
            
            // Create survey response
            SurveyResponse::create([
                'participant_id' => $participant->id,
                'survey_id' => $survey->id,
                'answers' => json_encode($validatedAnswers),
                'submitted_at' => now(),
            ]);
            
            // Mark token as used
            $surveyToken->update([
                'used' => true,
                'used_at' => now()
            ]);
            
            // Mark survey as completed and generate certificate
            $participant->completeSurvey();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Survey submitted successfully!',
                'redirect' => route('survey.success', ['token' => $token])
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Survey submission error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the survey.'
            ], 500);
        }
    }
    
    public function success($token)
    {
        return view('survey.success', [
            'token' => $token,
            'message' => 'Thank you for completing the survey!',
            'subtitle' => 'Your certificate will be emailed to you shortly.'
        ]);
    }
}