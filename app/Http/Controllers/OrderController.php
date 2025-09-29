<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'voucher_code'   => 'nullable|string',
            'payment_method' => 'required|string|in:COD,online', 
        ]);

        $user      = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('medication')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'السلة فارغة، لا يمكن تنفيذ الطلب'], 400);
        }

        
        $subtotal = $cartItems->sum(fn($item) => $item->medication->price * $item->quantity);
        $discount = 0;
        $voucher  = null;

        
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)->first();

            if ($voucher && $this->isValidVoucher($voucher)) {
                $discount = $voucher->type === 'fixed'
                ? $voucher->discount
                : ($subtotal * ($voucher->discount / 100));

                $voucher->increment('used_count'); 
            } else {
                return response()->json(['message' => 'القسيمة غير صالحة أو منتهية الصلاحية'], 400);
            }
        }

        $total = max(0, $subtotal - $discount);

        
        $order = Order::create([
            'user_id'        => $user->id,
            'address_id'     => $request->address_id,
            'voucher_id'     => $voucher ? $voucher->id : null,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'payment_method' => $request->payment_method, 
            'status'         => 'pending',                
        ]);

        
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'تم تنفيذ الطلب بنجاح، الدفع عند الاستلام',
            'order'   => $order,
        ]);
    }

    public function activeOrders()
    {
        $user = Auth::user();

        
        $orders = Order::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('status', 'pending')
                    ->orWhere('status', 'processing');
            })
            ->get();

        foreach ($orders as $order) {
            if ($order->status == 'pending' && $order->created_at->lt(now()->subHours(24))) {
                $order->status = 'completed';
                $order->save();
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب الطلبات النشطة بنجاح',
            'orders'  => $orders,
        ]);
    }

    

    public function PastOrders()
    {
        $user = Auth::user();
    
        
        $pastOrders = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'message'     => 'تم جلب الطلبات السابقة التي تم تسليمها بنجاح',
            'past_orders' => $pastOrders,
        ]);
    }
    
  
}
