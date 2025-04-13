<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    //

    public function getNearbyPharmacies(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        $pharmacies = Pharmacy::select('*')
            ->selectRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance', [
                $latitude, $longitude, $latitude,
            ])
            ->having('distance', '<', 10) // 10 كم
            ->orderBy('distance', 'asc')
            ->get();

        return response()->json($pharmacies);
    }

    public function getAllPharmacies()
    {
        // استرجاع جميع الصيدليات من قاعدة البيانات
        $pharmacies = Pharmacy::all();

        return response()->json(['pharmacies' => $pharmacies], 200);
    }

}
