<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is regular user
    public function isUser()
    {
        return $this->role === 'user';
    }

    // ✅ ADD THESE RELATIONSHIPS:

    // User has many events (as admin/creator)
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // User has many participants (registrations)
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    // Get events user is registered for
    public function registeredEvents()
    {
        return $this->belongsToMany(Event::class, 'participants')
                    ->withPivot('checked_in', 'checked_out', 'survey_completed')
                    ->withTimestamps();
    }

    // Get completed surveys
    public function surveyResponses()
    {
        return $this->hasManyThrough(SurveyResponse::class, Participant::class);
    }

    // Get certificates
    public function certificates()
    {
        return $this->hasManyThrough(Certificate::class, Participant::class);
    }

    // Check if user is registered for an event
    public function isRegisteredForEvent($eventId)
    {
        return $this->participants()->where('event_id', $eventId)->exists();
    }

    // Get participant record for an event
    public function getParticipantForEvent($eventId)
    {
        return $this->participants()->where('event_id', $eventId)->first();
    }
}