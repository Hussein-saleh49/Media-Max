<?php
namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'nullable|string|max:255',
            'address'          => 'required|string',
            'building_number'  => 'nullable|string|max:10',
            'floor_number'     => 'nullable|string|max:10',
            'apartment_number' => 'nullable|string|max:10',
        ]);

        
        $address = Address::create([
            'user_id'          => Auth::id(),
            'title'            => $request->title,
            'address'          => $request->address,
            'building_number'  => $request->building_number,
            'floor_number'     => $request->floor_number,
            'apartment_number' => $request->apartment_number,
        ]);

        return response()->json(['message' => 'تم حفظ العنوان', 'address' => $address]);
    }




    
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'ليس لديك الصلاحية لتحديث هذا العنوان'], 403);
        }
    
        $request->validate([
            'title'            => 'nullable|string|max:255',
            'address'          => 'required|string',
            'building_number'  => 'nullable|string|max:10',
            'floor_number'     => 'nullable|string|max:10',
            'apartment_number' => 'nullable|string|max:10',
        ]);
    
        $address->update($request->all());
    
        return response()->json(['message' => 'تم تحديث العنوان', 'address' => $address]);
    }
    
    public function getAddresses()
    {
        $addresses = Address::where('user_id', Auth::id())->get();

        return response()->json(['addresses' => $addresses]);
    }

    public function deleteAddress(Request $request)
    {
        
        $user = Auth::user();

        
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        
        $address = Address::where('id', $request->address_id)
                          ->where('user_id', $user->id)
                          ->first();

        if (!$address) {
            return response()->json(['message' => 'العنوان غير موجود أو لا يخصك'], 404);
        }

        
        $address->delete();

        return response()->json(['message' => 'تم حذف العنوان بنجاح']);
    }

}
