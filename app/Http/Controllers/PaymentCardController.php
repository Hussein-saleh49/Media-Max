<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentCard;
use Illuminate\Support\Facades\Auth;

class PaymentCardController extends Controller
{
    
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
            'card_number' => $request->card_number, 
            'expiry_date' => $request->expiry_date,
            'cvv' => $request->cvv, 
            'card_type' => $request->card_type
        ]);

        return response()->json(['message' => 'تمت إضافة البطاقة بنجاح', 'card' => $card]);
    }

    
    public function index()
    {
        $cards = PaymentCard::where('user_id', Auth::id())->get();
        return response()->json($cards);
    }

    // 
    public function update(Request $request, $id)
    {
        $card = PaymentCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'card_holder_name' => 'required|string|max:255', 
            'card_number'      => 'required|string|digits:16|unique:payment_cards,card_number,' . ($id ?? 'NULL') . ',id',
            'expiry_date'      => 'required|string|max:5|regex:/^\d{2}\/\d{2}$/', 
            'cvv'             => 'required|string|digits_between:3,4', 
            'card_type'       => 'nullable|string|max:50', 
        ]);
        

        $card->update($request->all());

        return response()->json(['message' => 'تم تحديث البطاقة بنجاح', 'card' => $card]);
    }

    
    public function destroy($id)
    {
        $card = PaymentCard::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $card->delete();

        return response()->json(['message' => 'تم حذف البطاقة بنجاح']);
    }
}
