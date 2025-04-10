<?php
namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    // ✅ البحث عن الأدوية
    public function search(Request $request)
    {
        $query = $request->input('query');

        // 🔍 البحث فقط عن الأدوية التي تطابق الاسم بالضبط
        $medication = Medication::where('name', $query)
            ->orWhere('generic_name', $query)
            ->orWhere('active_ingredient', $query)
            ->first();

        // 📌 إذا لم يتم العثور على الدواء
        if (! $medication) {
            return response()->json(['message' => 'لم يتم العثور على الدواء المطلوب'], 404);
        }

        // ✅ زيادة عدد مرات البحث
        $medication->increment('search_count');

        return response()->json($medication);
    }

    public function topSearch(Request $request)
    {
        $limit = $request->input('limit', 10); // عدد النتائج (افتراضي 10)

        $topMedications = Medication::orderBy('search_count', 'desc')
            ->limit($limit)
            ->get(['name', 'generic_name', 'active_ingredient', 'search_count']);

        return response()->json($topMedications);
    }

}
