<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // إنشاء موعد جديد
    public function store(Request $request)
    {
        $request->validate([
            'medication_name' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'appointment_time' => 'required|date',
        ]);

        $appointment = Appointment::create($request->all());

        return response()->json(['message' => 'Appointment created', 'data' => $appointment]);
    }

    // عرض المواعيد الخاصة باليوم فقط
    public function index()
    {
        $todayAppointments = Appointment::whereDate('appointment_time', today())->get();

        return response()->json(['data' => $todayAppointments]);
    }

    // تحديث حالة الموعد (taken أو skipped)
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:taken,skipped',
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return response()->json(['message' => 'Status updated', 'data' => $appointment]);
    }
}
