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

    // Event belongs to a user (admin who created it)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Event has one survey
    public function survey()
    {
        return $this->hasOne(Survey::class);
    }

    // Event has many participants
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    // Event has many certificates
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Format date for display
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F d, Y');
    }

    // Format time for display
    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('h:i A') : 'TBD';
    }

    // Check if event is full
    public function getIsFullAttribute()
    {
        if (!$this->capacity) return false;
        return $this->participants()->count() >= $this->capacity;
    }

    // Get available spots
    public function getAvailableSpotsAttribute()
    {
        if (!$this->capacity) return 'Unlimited';
        $registered = $this->participants()->count();
        return max(0, $this->capacity - $registered);
    }
}