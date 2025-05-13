<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // ✅ جلب جميع الفئات
    public function index()
    {
        $categories = Category::orderBy('priority', 'desc')->get();
        return response()->json($categories);
    }

    // ✅ إضافة فئة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'priority' => $request->priority ?? 0, // الافتراضي 0
            'product_count' => 0,
        ]);

        return response()->json([
            'message' => 'تمت إضافة الفئة بنجاح!',
            'category' => $category
        ]);
    }

    // ✅ عرض فئة معينة
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة!'], 404);
        }

        return response()->json($category);
    }

    // ✅ تحديث فئة
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة!'], 404);
        }

        $request->validate([
            'name' => 'string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $category->update($request->all());

        return response()->json([
            'message' => 'تم تحديث الفئة بنجاح!',
            'category' => $category
        ]);
    }

    // ✅ حذف فئة
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة!'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'تم حذف الفئة بنجاح!']);
    }
}
