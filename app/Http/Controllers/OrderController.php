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
            'payment_method' => 'required|string|in:COD,online', // الدفع عند الاستلام فقط
        ]);

        $user      = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('medication')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'السلة فارغة، لا يمكن تنفيذ الطلب'], 400);
        }

        // حساب المجموع الفرعي
        $subtotal = $cartItems->sum(fn($item) => $item->medication->price * $item->quantity);
        $discount = 0;
        $voucher  = null;

        // التحقق من القسيمة وتطبيقها
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', $request->voucher_code)->first();

            if ($voucher && $this->isValidVoucher($voucher)) {
                $discount = $voucher->type === 'fixed'
                ? $voucher->discount
                : ($subtotal * ($voucher->discount / 100));

                $voucher->increment('used_count'); // تحديث عدد مرات الاستخدام
            } else {
                return response()->json(['message' => 'القسيمة غير صالحة أو منتهية الصلاحية'], 400);
            }
        }

        $total = max(0, $subtotal - $discount);

        // إنشاء الطلب
        $order = Order::create([
            'user_id'        => $user->id,
            'address_id'     => $request->address_id,
            'voucher_id'     => $voucher ? $voucher->id : null,
            'subtotal'       => $subtotal,
            'discount'       => $discount,
            'total'          => $total,
            'payment_method' => $request->payment_method, // يختار المستخدم "COD" أو "Online"
            'status'         => 'pending',                // الحالة الافتراضية
        ]);

        // تفريغ السلة بعد الطلب
        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'تم تنفيذ الطلب بنجاح، الدفع عند الاستلام',
            'order'   => $order,
        ]);
    }

    public function activeOrders()
    {
        $user = Auth::user();

        // جلب الطلبات النشطة ولكن تحديث حالة الطلبات القديمة تلقائيًا
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

    // ✅ جلب الطلبات السابقة (الطلبات المكتملة)

    public function PastOrders()
    {
        $user = Auth::user();
    
        // جلب الطلبات التي حالتها "delivered"
        $pastOrders = Order::where('user_id', $user->id)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()->json([
            'message'     => 'تم جلب الطلبات السابقة التي تم تسليمها بنجاح',
            'past_orders' => $pastOrders,
        ]);
    }
    
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'transaction_id' => 'required|string', // رقم عملية الدفع من بوابة الدفع
        ]);

        $order = Order::find($request->order_id);

        if ($order->payment_method !== 'online') {
            return response()->json(['message' => 'هذا الطلب ليس للدفع الإلكتروني!'], 400);
        }

        // تحديث حالة الطلب بعد الدفع الناجح
        $order->update([
            'status'         => 'paid',
            'transaction_id' => $request->transaction_id, // تخزين رقم المعاملة
        ]);

        return response()->json(['message' => 'تم تأكيد الدفع بنجاح', 'order' => $order], 200);
    }

    /**
     * التحقق من صلاحية القسيمة
     */
    private function isValidVoucher(Voucher $voucher)
    {
        return ($voucher->expiration_date === null || now()->lessThan($voucher->expiration_date))
            && ($voucher->used_count < $voucher->usage_limit);
    }

}
