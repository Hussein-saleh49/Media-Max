<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DriverRating;
use App\Models\Driver; 
use Illuminate\Support\Facades\Auth;

class DriverRatingController extends Controller
{
    public function rateDriver(Request $request)
    {
        
        $request->validate([
            'driver_id' => 'required|exists:drivers,id', 
            'rating'    => 'required|integer|min:1|max:5', 
            'feedback'  => 'nullable|string|max:1000',  
        ]);

    
        $driver = Driver::findOrFail($request->driver_id);

        
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
