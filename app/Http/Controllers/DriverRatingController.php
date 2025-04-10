<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverRating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DriverRatingController extends Controller
{
    public function rateDriver(Request $request)
    {
        // ✅ التحقق من صحة البيانات المدخلة
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'rating'    => 'required|integer|min:1|max:5',
            'feedback'  => 'nullable|string|max:1000',
        ]);

        // ✅ جلب المستخدم من قاعدة البيانات
        $driver = User::findOrFail($request->driver_id);

        // ✅ التحقق من أن هذا المستخدم هو سائق
        if ($driver->role !== 'driver') {
            return response()->json(['message' => 'هذا المستخدم ليس سائقًا!'], 400);
        }

        // ✅ إنشاء تقييم جديد
        $rating = DriverRating::create([
            'user_id'   => Auth::id(),  // المقيِّم هو المستخدم الحالي
            'driver_id' => $driver->id, // السائق الذي تم تقييمه
            'rating'    => $request->rating,
            'feedback'  => $request->feedback,
        ]);

        return response()->json([
            'message' => 'تم إرسال التقييم بنجاح',
            'rating'  => $rating
        ], 201);
    }
}
