<?php
namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    
    // 
public function search(Request $request)
{
    $query = $request->input('query');

    
    $medication = Medication::where('name', 'like', "%$query%")
        ->orWhere('arabic_name', 'like', "%$query%")
        ->orWhere('generic_name', 'like', "%$query%")
        ->first();

    
    if (!$medication) {
        $alternatives = Medication::where('active_ingredient', 'like', "%$query%")
            ->pluck('name');

        if ($alternatives->isEmpty()) {
            return response()->json(['message' => 'لم يتم العثور على أي دواء مطابق للبحث.'], 404);
        }

        return response()->json([
            'message' => 'لم يتم العثور على دواء بالاسم، ولكن وُجدت أدوية تحتوي على نفس المادة الفعالة.',
            'alternatives' => $alternatives
        ]);
    }

    
    $medication->increment('search_count');

    
    $pharmacies = $medication->pharmacies()
        ->wherePivot('stock', '>', 0)
        ->get()
        ->map(function ($pharmacy) {
            return [
                'id' => $pharmacy->id,
                'name' => $pharmacy->name,
                'address' => $pharmacy->address,
                'phone' => $pharmacy->phone,
                'image_url' => $pharmacy->image ? url($pharmacy->image) : null,
            ];
        });


    $response = [
        'medication' => [
            'id' => $medication->id,
            'name' => $medication->name,
            'arabic_name' => $medication->arabic_name,
            'generic_name' => $medication->generic_name,
            'active_ingredient' => $medication->active_ingredient,
            'price' => $medication->price,
            'image_url' => $medication->image ? url($medication->image) : null,
        ],
        'available_in_pharmacies' => $pharmacies,
    ];

    
    if ($pharmacies->isEmpty()) {
        $alternatives = Medication::where('active_ingredient', $medication->active_ingredient)
            ->where('id', '!=', $medication->id)
            ->pluck('name');

        $response['alternatives'] = $alternatives;
        $response['message'] = ' هذا الدواء غير متوفر حالياً. جرّب استخدام بديل يحتوي على نفس المادة الفعالة: "' . $medication->active_ingredient . '"';
    }

    return response()->json($response);
}




  public function searchInPharmacy(Request $request, $pharmacyId)
{
    $query = $request->input('query');


    $medication = Medication::where('name', 'like', "%$query%")
        ->orWhere('arabic_name', 'like', "%$query%")
        ->orWhere('generic_name', 'like', "%$query%")
        ->orWhere('active_ingredient', 'like', "%$query%")
        ->first();

    
    if (!$medication) {
        return response()->json(['message' => 'لم يتم العثور على الدواء المطلوب'], 404);
    }

    
    $pharmacy = $medication->pharmacies()
        ->wherePivot('pharmacy_id', $pharmacyId)
        ->wherePivot('stock', '>', 0)
        ->first();

    if (!$pharmacy) {
        return response()->json(['message' => 'الدواء غير متوفر في هذه الصيدلية'], 404);
    }

    
    return response()->json([
        'message' => 'تم العثور على الدواء في الصيدلية',
        'medication' => [
            'id' => $medication->id,
            'name' => $medication->name,
            'arabic_name' => $medication->arabic_name,
            'generic_name' => $medication->generic_name,
            'active_ingredient' => $medication->active_ingredient,
            'price' => $medication->price,
            'image_url' => $medication->image ? url($medication->image) : null,
        ],
        'pharmacy' => [
            'id' => $pharmacy->id,
            'name' => $pharmacy->name,
            'address' => $pharmacy->address,
            'phone' => $pharmacy->phone,
            'image_url' => $pharmacy->image ? url($pharmacy->image) : null,
        ]
    ]);
}

   


  public function topSearch(Request $request)
{
    $limit = $request->input('limit', 25);

    $topMedications = Medication::orderBy('search_count', 'desc')
        ->limit($limit)
        ->get([
            'name',
            'arabic_name',
            'active_ingredient',
            'search_count',
            'price',
            'image',
            'capsules_number' 
        ]);

    
    $data = $topMedications->map(function ($med) {
        return [
            'name' => $med->name,
            'arabic_name' => $med->arabic_name,
            'active_ingredient' => $med->active_ingredient,
            'search_count' => $med->search_count,
            'price' => $med->price,
            'capsules_number' => $med->capsules_number, 
            'image_url' => $med->image ? url($med->image) : null,
        ];
    });

    return response()->json($data);
}


public function store(Request $request)
{
    
    if (!auth()->user() || auth()->user()->role !== 'pharmacist') {
        return response()->json(['message' => 'أنت غير مخول لإضافة الأدوية.'], 403);
    }

    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'quantity' => 'nullable|integer',
        'what_does_it_treat' => 'nullable|string|max:255',
        'pharmacy_id' => 'required|exists:pharmacies,id',
        
    ]);

    
    $imagePath = null;

    
    $data = [
        'name' => $validated['name'],
        'capsules_number' => $validated['quantity'] ?? null,
        'category' => $validated['what_does_it_treat'] ?? null,
        'dosage_form' => 'غير محدد',
        'image' => $imagePath, 
    ];


    $medication = Medication::create($data);

    
    $stock = $validated['quantity'] ?? 0;
    $medication->pharmacies()->attach($validated['pharmacy_id'], ['stock' => $stock]);

    
    return response()->json([
        'message' => 'تم إضافة الدواء وربطه بالصيدلية بنجاح',
        'data' => [
            'id' => $medication->id,
            'name' => $medication->name,
            'capsules_number' => $medication->capsules_number,
            'category' => $medication->category,
            'image_url' => null, 
        ]
    ], 201);
}










public function getAllPharmacies()
{
    $pharmacies = Pharmacy::all()->map(function ($pharmacy) {
        return [
            'id' => $pharmacy->id,
            'name' => $pharmacy->name,
            'address' => $pharmacy->address,
            'phone' => $pharmacy->phone,
            'image_url' => $pharmacy->image ? url($pharmacy->image) : null,
        ];
    });

    return response()->json($pharmacies);
}



public function update(Request $request, $id)
{
    $medication = Medication::findOrFail($id);

    $validatedData = $request->validate([
        'name' => 'sometimes|string|max:255',
        'arabic_name' => 'sometimes|string|max:255',
        'generic_name' => 'sometimes|nullable|string|max:255',
        'active_ingredient' => 'sometimes|nullable|string|max:255',
        'manufacturer' => 'sometimes|nullable|string|max:255',
        'dosage_form' => 'sometimes|string|max:255',
        'category' => 'sometimes|string|max:255',
        'search_count' => 'sometimes|nullable|integer',
        'capsules_number' => 'sometimes|nullable|integer',
        'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'price' => 'sometimes|nullable|numeric',
    ]);

    $medication->fill($validatedData);

    
    if ($request->hasFile('image')) {

        if ($medication->image && file_exists(public_path($medication->image))) {
            unlink(public_path($medication->image));
        }

        
        $destinationPath = public_path('uploads/medications');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

    
        $image = $request->file('image');
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);
        $filename = time() . '_' . $sanitizedName . '.' . $extension;

        
        $image->move($destinationPath, $filename);
        $medication->image = 'uploads/medications/' . $filename;
    }


    $medication->save();

    return response()->json([
        'message' => 'تم تحديث الدواء بنجاح',
        'data' => [
            'id' => $medication->id,
            'name' => $medication->name,
            'arabic_name' => $medication->arabic_name,
            'generic_name' => $medication->generic_name,
            'active_ingredient' => $medication->active_ingredient,
            'manufacturer' => $medication->manufacturer,
            'dosage_form' => $medication->dosage_form,
            'category' => $medication->category,
            'search_count' => $medication->search_count,
            'capsules_number' => $medication->capsules_number,
            'price' => $medication->price,
            'image_url' => $medication->image ? url($medication->image) : null,
        ]
    ]);
}



public function destroy($id)
{
    
    $medication = Medication::find($id);

    if (!$medication) {
        return response()->json(['message' => 'الدواء غير موجود.'], 404);
    }

    
    if ($medication->image && file_exists(public_path($medication->image))) {
        unlink(public_path($medication->image));
    }

    
    $medication->pharmacies()->detach();

    
    $medication->delete();

    return response()->json(['message' => 'تم حذف الدواء بنجاح.'], 200);
}

    }

