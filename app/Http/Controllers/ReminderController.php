<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReminderController extends Controller
{
    // making reminder
    public function store(Request $request)
    {
        // check validated data
        $request->validate([
            'medication_name' => 'required|string',
            'reminder_date' => 'required|date',
            'reminder_time' => 'required|date_format:H:i:s',
            'am_pm' => 'required|in:AM,PM',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'sound' => 'nullable|string',
            'label' => 'nullable|string',
            'ring_duration' => 'nullable|integer',
            'snooze_duration' => 'nullable|integer',
        ]);
    
        // combine date and time
        $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $request->reminder_date . ' ' . $request->reminder_time
        );
    
        $userId = auth()->id();
    
        // check if there is an appointment for that reminder
        $appointment = Appointment::where('user_id', $userId)
            ->where('medication_name', $request->medication_name)
            ->where('appointment_date', $reminderDateTime->toDateString())  
            ->where('appointment_time', $reminderDateTime->toTimeString()) 
            ->first();
    
        //
        if (!$appointment) {
            // 
            $appointment = Appointment::create([
                'user_id' => $userId,
                'medication_name' => $request->medication_name,
                'appointment_date' => $reminderDateTime->toDateString(),
                'appointment_time' => $reminderDateTime->toTimeString(),
                'status' => 'skipped', 
            ]);
        }
    
        // make reminder and connect with appointment
        $reminder = Reminder::create([
            'user_id' => $userId,
            'appointment_id' => $appointment->id,
            'medication_name' => $request->medication_name,
            'reminder_date' => $reminderDateTime->toDateString(),
            'reminder_time' => $reminderDateTime->toTimeString(),
            'am_pm' => $request->am_pm,
            'repeat_days' => $request->repeat_days ?? null,
            'sound' => $request->sound,
            'label' => $request->label,
            'ring_duration' => $request->ring_duration,
            'snooze_duration' => $request->snooze_duration,
        ]);
    
        return response()->json([
            'message' => 'Reminder created and linked to appointment successfully',
            'reminder' => $reminder,
            'appointment' => $appointment
        ]);
    }
    public function dailyProgress(Request $request)
    {
        // get the current user
        $user = $request->user();

        // bringing reminders for one user for that day
        $todayReminders = Reminder::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString()) // تصفية حسب تاريخ اليوم
            ->get();

        //check if there is data
        if ($todayReminders->isEmpty()) {
            return response()->json([
                'message' => 'there is no reminder for that day',
                'progress' => [],
            ], 200);
        }

        return response()->json([
            'message' => 'here is your reminder',
            'progress' => $todayReminders,
        ], 200);
    }

}
