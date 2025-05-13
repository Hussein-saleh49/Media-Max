<?php
namespace App\Http\Controllers;


use App\Models\Cart;
use App\Models\Medication;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // 🛒 إضافة دواء إلى السلة
   public function addToCart(Request $request)
{
    $request->validate([
        'medication_id' => 'required|exists:medications,id',
        'quantity' => 'required|integer|min:1'
    ]);

    // البحث عن العنصر في السلة للمستخدم الحالي
    $cartItem = Cart::where('user_id', auth()->id())
        ->where('medication_id', $request->medication_id)
        ->first();

    if ($cartItem) {
        // تحديث الكمية يدويًا
        $cartItem->quantity += $request->quantity;
        $cartItem->save();
    } else {
        // إنشاء عنصر جديد في السلة
        $cartItem = Cart::create([
            'user_id' => auth()->id(),
            'medication_id' => $request->medication_id,
            'quantity' => $request->quantity,
        ]);
    }

    return response()->json(['message' => 'تمت إضافة الدواء إلى السلة', 'cart' => $cartItem]);
}

    // 🛍️ جلب السلة
    public function getCart()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('medication') // جلب معلومات الدواء مع العنصر في السلة
            ->get();
    
        // حساب الإجمالي الفرعي (subtotal)
        $subtotal = $cartItems->sum(function ($cartItem) {
            return $cartItem->medication->price * $cartItem->quantity;
        });
    
        return response()->json([
            'cart' => $cartItems,
            'subtotal' => number_format($subtotal, 2) // تحديد دقة الرقم العشري
        ]);
    }
    

    // 🔄 تحديث الكمية
    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('id', $request->cart_id)->where('user_id', auth()->id())->firstOrFail();
        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'تم تحديث الكمية', 'cart' => $cartItem]);
    }

    // ❌ حذف عنصر من السلة
    public function removeFromCart($cartId)
    {
        $cartItem = Cart::where('id', $cartId)->where('user_id', auth()->id())->firstOrFail();
        $cartItem->delete();

        return response()->json(['message' => 'تم حذف الدواء من السلة']);
    }
}
