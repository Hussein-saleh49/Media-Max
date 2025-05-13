<?php
namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    
    // ✅ البحث عن الأدوية
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        // البحث في الأدوية باستخدام الاسم أو الاسم العربي أو الاسم العام أو المادة الفعالة
        $medication = Medication::where('name', 'like', "%$query%")
            ->orWhere('arabic_name', 'like', "%$query%")
            ->orWhere('generic_name', 'like', "%$query%")
            ->orWhere('active_ingredient', 'like', "%$query%")
            ->first();
    
        // التحقق إذا لم يتم العثور على الدواء
        if (!$medication) {
            return response()->json(['message' => 'لم يتم العثور على الدواء المطلوب'], 404);
        }
    
        // زيادة عدد مرات البحث في جدول الأدوية
        $medication->increment('search_count');
    
        // جلب الصيدليات التي تحتوي على هذا الدواء مع كمية أكبر من 0
        $pharmacies = $medication->pharmacies()
            ->wherePivot('stock', '>', 0)
            ->get()
            ->map(function ($pharmacy) {
                // إزالة بيانات الـ Pivot من الاستجابة
                return $pharmacy->makeHidden('pivot');
            });
    
        return response()->json([
            'medication'              => $medication,
            'available_in_pharmacies' => $pharmacies,
        ]);
    }
    
    public function searchInPharmacy(Request $request, $pharmacyId)
{
    $query = $request->input('query');

    // البحث عن الدواء في جدول الأدوية
    $medication = Medication::where('name', 'like', "%$query%")
        ->orWhere('arabic_name', 'like', "%$query%")
        ->orWhere('generic_name', 'like', "%$query%")
        ->orWhere('active_ingredient', 'like', "%$query%")
        ->first();

    // إذا لم يتم العثور على الدواء
    if (!$medication) {
        return response()->json(['message' => 'لم يتم العثور على الدواء المطلوب'], 404);
    }

    // البحث عن الدواء في الصيدلية المحددة مع التحقق من الكمية
    $pharmacy = $medication->pharmacies()
        ->wherePivot('pharmacy_id', $pharmacyId)
        ->wherePivot('stock', '>', 0)
        ->first();

    if (!$pharmacy) {
        return response()->json(['message' => 'الدواء غير متوفر في هذه الصيدلية'], 404);
    }

    // إرجاع بيانات الدواء مع معلومات الصيدلية
    return response()->json([
        'message' => 'تم العثور على الدواء في الصيدلية',
        'medication' => $medication,
        'pharmacy' => $pharmacy->makeHidden('pivot')
    ]);
}


   


    public function topSearch(Request $request)
    {
        $limit = $request->input('limit', 10); // عدد النتائج (افتراضي 10)

        $topMedications = Medication::orderBy('search_count', 'desc')
            ->limit($limit)
            ->get(['name', 'generic_name', 'active_ingredient', 'search_count']);

        return response()->json($topMedications);
    }


    public function store(Request $request)
    {
        // تحقق من أن المستخدم لديه دور 'pharmacist'
        if (auth()->user()->role !== 'pharmacist') {
            return response()->json(['message' => 'أنت غير مخول لإضافة الأدوية.'], 403);
        }

        // تحقق من المدخلات
        $request->validate([
            'name' => 'required|string|max:255',
            'arabic_name' => 'nullable|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage_form' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // إنشاء دواء جديد
        $medication = Medication::create([
            'name' => $request->input('name'),
            'arabic_name' => $request->input('arabic_name'),
            'generic_name' => $request->input('generic_name'),
            'active_ingredient' => $request->input('active_ingredient'),
            'manufacturer' => $request->input('manufacturer'),
            'dosage_form' => $request->input('dosage_form'),
            'category' => $request->input('category'),
        ]);

        return response()->json(['message' => 'تم إضافة الدواء بنجاح', 'data' => $medication], 201);
    }
}

