<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'event_id',
        'questions',
        'is_active',
        'send_on_checkin',
        'send_on_checkout',
    ];

    protected $casts = [
        'questions' => 'array', // This should cast JSON to array automatically
        'is_active' => 'boolean',
        'send_on_checkin' => 'boolean',
        'send_on_checkout' => 'boolean',
    ];

    // Survey belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Survey has many responses
    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // Get question count - FIXED VERSION
    public function getQuestionCountAttribute()
    {
        if (is_string($this->questions)) {
            // If questions is still a string, decode it
            $questions = json_decode($this->questions, true);
            return is_array($questions) ? count($questions) : 0;
        }
        
        if (is_array($this->questions)) {
            return count($this->questions);
        }
        
        return 0;
    }

    // Helper to get questions as array
    public function getQuestionsArrayAttribute()
    {
        if (is_string($this->questions)) {
            return json_decode($this->questions, true) ?: [];
        }
        
        if (is_array($this->questions)) {
            return $this->questions;
        }
        
        return [];
    }
}