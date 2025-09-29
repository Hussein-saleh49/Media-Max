<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'medication_name',
        'title',
        'description',
        'appointment_time',
        'status',
    ];

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
