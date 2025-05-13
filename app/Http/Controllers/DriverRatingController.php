<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverRating;
use App\Models\Driver; // استخدام نموذج السائق
use Illuminate\Support\Facades\Auth;

class DriverRatingController extends Controller
{
    public function rateDriver(Request $request)
    {
        // ✅ التحقق من صحة البيانات المدخلة
        $request->validate([
            'driver_id' => 'required|exists:drivers,id', // ربط السائق مع جدول drivers
            'rating'    => 'required|integer|min:1|max:5', // التقييم بين 1 و 5
            'feedback'  => 'nullable|string|max:1000',  // الملاحظات اختيارية
        ]);

        // ✅ جلب السائق من قاعدة البيانات
        $driver = Driver::findOrFail($request->driver_id);

        // ✅ إنشاء تقييم جديد
        $rating = DriverRating::create([
            'driver_id' => $request->driver_id,
            'rating' => $request->rating,
            'feedback' => $request->feedback,
        ]);
        

        return response()->json([
            'message' => 'تم إرسال التقييم بنجاح',
            'rating'  => $rating
        ], 201);
    }
}
