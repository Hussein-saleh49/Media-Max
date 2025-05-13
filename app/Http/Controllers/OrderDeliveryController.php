<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Driver;
use Illuminate\Http\Request;

class OrderDeliveryController extends Controller
{
    public function confirmDelivery(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'order_id'  => 'required|exists:orders,id',  // تحقق من وجود order_id في جدول orders
            'driver_id' => 'required|exists:drivers,id',  // تحقق من وجود driver_id في جدول drivers
        ]);

        // الحصول على السائق ومعرف الطلب
        $driver = Driver::find($request->driver_id);  // جلب السائق بناءً على معرّف السائق
        $order  = Order::find($request->order_id);    // جلب الطلب بناءً على معرّف الطلب

        // التحقق من حالة الطلب إذا كان تم تسليمه مسبقًا
        if ($order->status === 'delivered') {
            return response()->json(['message' => 'تم تسليم الطلب مسبقًا'], 400);
        }

        // تحديث حالة الطلب
        $order->status       = 'delivered';               // تغيير حالة الطلب إلى "تم التسليم"
        $order->delivered_by = $driver->id;             // تخزين اسم السائق في الحقل delivered_by
        $order->delivered_at = now();                     // تخزين تاريخ ووقت التسليم
        $order->save();                                    // حفظ التغييرات في قاعدة البيانات

        // إرجاع الاستجابة مع تفاصيل السائق والطلب
        return response()->json([
            'message'  => 'تم تأكيد تسليم الطلب بنجاح',
            'driver'   => [
                'id'    => $driver->id,       // معرّف السائق
                'name'  => $driver->name,     // اسم السائق
                'email' => $driver->email,    // البريد الإلكتروني للسائق
                'phone' => $driver->phone,    // رقم الهاتف للسائق
            ],
            'order_id' => $order->id,           // معرّف الطلب
        ]);
    }
}
