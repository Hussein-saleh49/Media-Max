<?php
namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PharmacyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('pharmacy_images', 'public');
            $validated['image'] = $path;
        }

        $pharmacy = Pharmacy::create($validated);

        return response()->json([
            'message' => 'تمت إضافة الصيدلية بنجاح',
            'data' => $pharmacy
        ], 201);
    }

  public function update(Request $request, $id)
{
    $pharmacy = Pharmacy::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string',
        'phone' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('image')) {
        
        if ($pharmacy->image && file_exists(public_path($pharmacy->image))) {
            unlink(public_path($pharmacy->image));
        }

    
        $destinationPath = public_path('uploads/pharmacies');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        
        $image = $request->file('image');
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $filename = time() . '_' . $sanitizedName . '.' . $extension;

    
        $image->move($destinationPath, $filename);
        $validated['image'] = 'uploads/pharmacies/' . $filename;
    }

    
    $pharmacy->update($validated);

    return response()->json([
        'message' => 'تم تحديث بيانات الصيدلية بنجاح',
        'data' => [
            'id' => $pharmacy->id,
            'name' => $pharmacy->name,
            'address' => $pharmacy->address,
            'phone' => $pharmacy->phone,
            'image_url' => $pharmacy->image ? url($pharmacy->image) : null,
        ]
    ]);
}


public function getMedicationsByPharmacy($id)
{
    $pharmacy = Pharmacy::with(['medications' => function ($query) {
        $query->select('medications.id', 'name', 'arabic_name', 'active_ingredient', 'price', 'image');
    }])->find($id);

    if (!$pharmacy) {
        return response()->json(['message' => 'Pharmacy not found'], 404);
    }

    return response()->json([
        'id' => $pharmacy->id,
        'name' => $pharmacy->name,
        'address' => $pharmacy->address,
        'phone' => $pharmacy->phone,
        'image_url' => $pharmacy->image ? url($pharmacy->image) : null,
        'medications' => $pharmacy->medications->map(function ($med) {
            return [
                'id' => $med->id,
                'name' => $med->name,
                'arabic_name' => $med->arabic_name,
                'active_ingredient' => $med->active_ingredient,
                'price' => $med->price,
                'image_url' => $med->image ? url($med->image) : null,
            ];
        }),
    ]);
}

}
