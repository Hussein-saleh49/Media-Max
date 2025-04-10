<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Info;

class InfoController extends Controller
{
    // ✅ إضافة بيانات جديدة ("من نحن" أو "اتصل بنا")
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:about,contact|unique:info,type',
            'content' => 'required|string|min:10'
        ]);

        $info = Info::create([
            'type' => $request->type,
            'content' => $request->content
        ]);

        return response()->json([
            'message' => 'تمت إضافة المعلومات بنجاح',
            'info' => $info
        ], 201);
    }

    // ✅ عرض بيانات "من نحن" أو "اتصل بنا"
    public function show($type)
    {
        $info = Info::where('type', $type)->first();

        if (!$info) {
            return response()->json(['message' => 'لم يتم العثور على المعلومات المطلوبة'], 404);
        }

        return response()->json($info);
    }

    // ✅ تحديث بيانات "من نحن" أو "اتصل بنا"
    public function update(Request $request, $type)
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);

        $info = Info::updateOrCreate(
            ['type' => $type],
            ['content' => $request->content]
        );

        return response()->json(['message' => 'تم تحديث المعلومات بنجاح', 'info' => $info]);
    }
}
