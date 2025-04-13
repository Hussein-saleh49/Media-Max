<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // get all appointments for one user
    public function index()
    {
        return response()->json(Appointment::where('user_id', auth()->id())->get());
    }

    // add new one
    public function store(Request $request)
    {
        // check of validated data
        $request->validate([
            'medication_name' => 'required|string',
            'appointment_date' => 'required|date|date_format:Y-m-d',  // التأكد من أن التاريخ بالشكل الصحيح
            'appointment_time' => 'required|date_format:H:i:s', // التأكد من أن الوقت بالشكل الصحيح
        ]);
    
        $userId = auth()->id();
    
        // combining date and time
        try {
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->appointment_date . ' ' . $request->appointment_time);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid date or time format'], 400);
        }
    
        // check if there is appointment for that user at the same time
        $existingAppointment = Appointment::where('user_id', $userId)
            ->where('appointment_date', $appointmentDateTime->toDateString())
            ->where('appointment_time', $appointmentDateTime->toTimeString())
            ->first();
    
        // 
        if ($existingAppointment) {
            return response()->json([
                'message' => 'Appointment already exists.',
                'appointment' => $existingAppointment
            ]);
        }
    
        // add new one
        try {
            $appointment = Appointment::create([
                'user_id' => $userId,
                'medication_name' => $request->medication_name,  
                'appointment_date' => $appointmentDateTime->toDateString(),
                'appointment_time' => $appointmentDateTime->toTimeString(),
                'status' => 'skipped'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating appointment: ' . $e->getMessage()], 500);
        }
    
        return response()->json([
            'message' => 'Appointment created successfully.',
            'appointment' => $appointment
        ]);
    }
    
    // update satus to taken
    public function markAsTaken($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'taken';
        $appointment->save();

        return response()->json(['message' => 'Appointment marked as Taken successfully']);
    }

    // update status to skipped
    public function markAsSkipped($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'skipped';
        $appointment->save();

        return response()->json(['message' => 'Appointment marked as Skipped successfully']);
    }
}
