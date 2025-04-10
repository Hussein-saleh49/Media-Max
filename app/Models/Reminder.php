<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'medication_name',
        'appointment_date',
        'reminder_date',
        'reminder_time',
        'am_pm',
        'repeat',
        'sound',
        'label',
        'ring_duration',
        'snooze_duration',
        'is_skipped',
    ];

   

    /**
     * Relationship: Reminder belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Reminder belongs to an appointment (optional).
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
