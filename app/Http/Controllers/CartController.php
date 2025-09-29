<?php
namespace App\Http\Controllers;


use App\Models\Cart;
use App\Models\Medication;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // 
   public function addToCart(Request $request)
{
    $request->validate([
        'medication_id' => 'required|exists:medications,id',
        'quantity' => 'required|integer|min:1'
    ]);

    //
    $cartItem = Cart::where('user_id', auth()->id())
        ->where('medication_id', $request->medication_id)
        ->first();

    if ($cartItem) {
        
        $cartItem->quantity += $request->quantity;
        $cartItem->save();
    } else {
    
        $cartItem = Cart::create([
            'user_id' => auth()->id(),
            'medication_id' => $request->medication_id,
            'quantity' => $request->quantity,
        ]);
    }

    return response()->json(['message' => 'تمت إضافة الدواء إلى السلة', 'cart' => $cartItem]);
}

    
   public function getCart()
{
    $cartItems = Cart::where('user_id', auth()->id())
        ->with('medication') 
        ->get();

    
    $subtotal = $cartItems->sum(function ($cartItem) {
        return $cartItem->medication->price * $cartItem->quantity;
    });

    
    $cart = $cartItems->map(function ($item) {
        return [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'medication' => [
                'id' => $item->medication->id,
                'name' => $item->medication->name,
                'arabic_name' => $item->medication->arabic_name,
                'price' => $item->medication->price,
                'capsules_number' => $item->medication->capsules_number,
                'image_url' => $item->medication->image ? url($item->medication->image) : null,
            ],
        ];
    });

    return response()->json([
        'cart' => $cart,
        'subtotal' => number_format($subtotal, 2)
    ]);
}


    
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

    
    public function removeFromCart($cartId)
    {
        $cartItem = Cart::where('id', $cartId)->where('user_id', auth()->id())->firstOrFail();
        $cartItem->delete();

        return response()->json(['message' => 'تم حذف الدواء من السلة']);
    }
}
