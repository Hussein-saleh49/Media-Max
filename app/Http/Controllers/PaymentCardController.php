<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentCard;
use Illuminate\Support\Facades\Auth;

class PaymentCardController extends Controller
{
    // 🔹 إضافة بطاقة دفع جديدة
    public function store(Request $request)
    {
        $request->validate([
            'card_holder_name' => 'required|string|max:255',
            'card_number' => 'required|string|min:16|max:16|unique:payment_cards,card_number',
            'expiry_date' => 'required|string|max:5',
            'cvv' => 'required|string|min:3|max:4',
            'card_type' => 'nullable|string'
        ]);

        $card = PaymentCard::create([
            'user_id' => Auth::id(),
            'card_holder_name' => $request->card_holder_name,
            'card_number' => $request->card_number, // يتم تشفيرها تلقائيًا في الـ Model
            'expiry_date' => $request->expiry_date,
            'cvv' => $request->cvv, // يتم تشفيرها تلقائيًا في الـ Model
            'card_type' => $request->card_type
        ]);

        return response()->json(['message' => 'تمت إضافة البطاقة بنجاح', 'card' => $card]);
    }

    // 📝 جلب جميع البطاقات الخاصة بالمستخدم
    public function index()
    {
        $cards = PaymentCard::where('user_id', Auth::id())->get();
        return response()->json($cards);
    }

    // ✏️ تحديث بطاقة موجودة
    public function update(Request $request, $id)
    {
        $card = PaymentCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'card_holder_name' => 'required|string|max:255', // اجعلها مطلوبة
            'card_number'      => 'required|string|digits:16|unique:payment_cards,card_number,' . ($id ?? 'NULL') . ',id',
            'expiry_date'      => 'required|string|max:5|regex:/^\d{2}\/\d{2}$/', // تأكد من التنسيق (MM/YY)
            'cvv'             => 'required|string|digits_between:3,4', // يجب أن يكون بين 3 و 4 أرقام
            'card_type'       => 'nullable|string|max:50', // هذا الحقل يمكن أن يكون اختياريًا
        ]);
        

        $card->update($request->all());

        return response()->json(['message' => 'تم تحديث البطاقة بنجاح', 'card' => $card]);
    }

    // ❌ حذف بطاقة
    public function destroy($id)
    {
        $card = PaymentCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $card->delete();

        return response()->json(['message' => 'تم حذف البطاقة بنجاح']);
    }
}
