<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'registration_number',
        'checked_in',
        'checked_out',
        'checked_in_at',
        'checked_out_at',
        'survey_sent',
        'survey_completed',
        'certificate_sent',
        'additional_info',
    ];

    protected $casts = [
        'checked_in' => 'boolean',
        'checked_out' => 'boolean',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'survey_sent' => 'boolean',
        'survey_completed' => 'boolean',
        'certificate_sent' => 'boolean',
    ];

    // Participant belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Participant belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Participant has one survey response
    public function surveyResponse()
    {
        return $this->hasOne(SurveyResponse::class);
    }

    // Participant has one certificate
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    // Generate registration number
    public static function generateRegistrationNumber()
    {
        return 'REG-' . strtoupper(uniqid());
    }

    // Add these methods to the Participant model:

// Check in participant
public function checkIn()
{
    $this->update([
        'checked_in' => true,
        'checked_in_at' => now(),
        'survey_sent' => false, // Reset for new check-in
    ]);
    
    // Send survey if event survey is set to send on check-in
    if ($this->event->survey && $this->event->survey->send_on_checkin) {
        $this->sendSurvey();
    }
    
    return $this;
}

// Check out participant
public function checkOut()
{
    $this->update([
        'checked_out' => true,
        'checked_out_at' => now(),
    ]);
    
    // Send survey if event survey is set to send on check-out
    if ($this->event->survey && $this->event->survey->send_on_checkout && !$this->survey_sent) {
        $this->sendSurvey();
    }
    
    return $this;
}

// Send survey email
public function sendSurvey()
{
    // Generate unique survey link
    $token = \Str::random(60);
    \DB::table('survey_tokens')->insert([
        'participant_id' => $this->id,
        'token' => $token,
        'expires_at' => now()->addDays(7),
    ]);
    
     
    //  $surveyLink = "fgdfgdfgdgdgfggdgdg";
    // Send email (we'll implement this later)
    \Log::info("Survey sent to participant {$this->id}, Link: {$surveyLink}");
    
    $this->update(['survey_sent' => true]);
    
    return $surveyLink;
}

// Mark survey as completed
public function completeSurvey()
{
    $this->update(['survey_completed' => true]);
    
    // Generate certificate
    $this->generateCertificate();
    
    return $this;
}

// Generate certificate
public function generateCertificate()
{
    $certificate = Certificate::create([
        'certificate_number' => Certificate::generateCertificateNumber(),
        'participant_id' => $this->id,
        'event_id' => $this->event_id,
        'file_path' => "certificates/{$this->id}_" . time() . ".pdf",
        'generated_at' => now(),
    ]);
    
    // Send certificate email (implement later)
    \Log::info("Certificate generated for participant {$this->id}");
    
    $this->update(['certificate_sent' => true]);
    
    return $certificate;
}

// public function sendSurvey()
// {
//     try {
//         // Generate unique survey link
//         $token = \Str::random(60);
        
//         // TEMPORARY: Don't save to database yet
//         \Log::info("Survey token generated (not saved to DB): {$token}");
        
//         /*
//         // This will be used after table exists:
//         \DB::table('survey_tokens')->insert([
//             'participant_id' => $this->id,
//             'token' => $token,
//             'expires_at' => now()->addDays(7),
//             'created_at' => now(),
//             'updated_at' => now(),
//         ]);
//         */
        
//         $surveyLink = url("/survey/{$token}");
        
//         \Log::info("Survey link for participant {$this->id}: {$surveyLink}");
        
//         // Update participant
//         $this->update(['survey_sent' => true]);
        
//         return $surveyLink;
        
//     } catch (\Exception $e) {
//         \Log::error("Failed to send survey: " . $e->getMessage());
//         // Still mark as sent to avoid errors
//         $this->update(['survey_sent' => true]);
//         return false;
//     }
// }

}