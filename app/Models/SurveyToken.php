<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'token',
        'expires_at',
        'used',
        'used_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean'
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Check if token is valid
     */
    public function isValid()
    {
        return !$this->used && $this->expires_at->isFuture();
    }
}