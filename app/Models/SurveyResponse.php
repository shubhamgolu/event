<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'survey_id',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime',
    ];

    // Response belongs to a participant
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Response belongs to a survey
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}