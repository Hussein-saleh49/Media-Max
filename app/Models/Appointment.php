<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    
    protected $fillable = [
       'user_id', 'medication_name', 'appointment_date', 'appointment_time', 'status'
    ];

    public function reminders()
{
    return $this->hasMany(Reminder::class);
}

}
