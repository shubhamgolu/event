<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'participant_id',
        'event_id',
        'file_path',
        'generated_at',
        'emailed_at',
        'email_status',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    // Certificate belongs to a participant
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    // Certificate belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Generate certificate number
    public static function generateCertificateNumber()
    {
        return 'CERT-' . date('Ymd') . '-' . strtoupper(uniqid());
    }
}