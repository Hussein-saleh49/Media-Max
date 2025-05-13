<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherController extends Controller

{
    public function createVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'discount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
            'usage_limit' => 'nullable|integer|min:1',
            'expiration_date' => 'required|date|after:today' // ✅ التحقق من صحة التاريخ
        ]);
    
        $voucher = Voucher::create([
            'code' => $request->code,
            'discount' => $request->discount,
            'type' => $request->type,
            'usage_limit' => $request->usage_limit ?? 1,
            'expiration_date' => $request->expiration_date // ✅ تأكد من حفظه
        ]);
    
        return response()->json([
            'message' => 'تم إنشاء القسيمة بنجاح',
            'voucher' => $voucher
        ]);
    }
    

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);
    
        $voucher = Voucher::where('code', $request->code)->first();
    
        // 🔴 التحقق من وجود القسيمة
        if (!$voucher) {
            return response()->json(['message' => 'القسيمة غير موجودة'], 400);
        }
    
        // ⏳ التحقق من تاريخ انتهاء القسيمة
        if ($voucher->expiration_date && now()->greaterThan($voucher->expiration_date)) {
            return response()->json(['message' => 'القسيمة منتهية الصلاحية'], 400);
        }
    
        // 🔄 التحقق من عدد مرات الاستخدام
        if ($voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['message' => 'تم استخدام هذه القسيمة الحد الأقصى من المرات'], 400);
        }
    
        // 🏷️ حساب قيمة الخصم
        $discountAmount = $voucher->type === 'fixed' 
            ? $voucher->discount 
            : ($request->total * ($voucher->discount / 100));
    
        // 🛒 التأكد من أن المجموع لا يكون أقل من 0
        $newTotal = max(0, $request->total - $discountAmount);
    
        // ✅ تحديث عدد مرات الاستخدام
        $voucher->increment('used_count');
    
        return response()->json([
            'message' => 'تم تطبيق القسيمة بنجاح',
            'discount' => $discountAmount,
            'new_total' => $newTotal
        ]);
    }
    
}
