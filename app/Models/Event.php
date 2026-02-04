<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'date',
        'time',
        'type',
        'location',
        'meeting_link',
        'price',
        'capacity',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // ADD THIS METHOD: Event has many survey responses through survey
    public function surveyResponses()
    {
        return $this->hasManyThrough(
            SurveyResponse::class,
            Survey::class,
            'event_id',
            'survey_id',
            'id',
            'id'
        );
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F d, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('h:i A') : 'TBD';
    }

    public function getIsFullAttribute()
    {
        if (!$this->capacity) return false;
        return $this->participants()->count() >= $this->capacity;
    }

    public function getAvailableSpotsAttribute()
    {
        if (!$this->capacity) return 'Unlimited';
        $registered = $this->participants()->count();
        return max(0, $this->capacity - $registered);
    }
}