<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id()); // ✅ جلب المستخدم الحالي

        // ✅ التحقق من صحة البيانات المدخلة
        $request->validate([
            'name'            => 'required|string|max:255', // ❗️ اجعل الاسم إجباريًا
            'phone_number'    => 'nullable|string|max:20|unique:users,phone_number,' . $user->id,
            'profile_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ التأكد من أن `name` يحتوي على قيمة غير فارغة
        if (! $request->has('name') || empty($request->name)) {
            return response()->json(['message' => 'الاسم مطلوب ولا يمكن أن يكون فارغًا'], 400);
        }

        // ✅ تحديث الاسم مباشرةً
        $user->name = $request->name;

        // ✅ تحديث رقم الهاتف إن وُجد
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        // ✅ تحديث الصورة الشخصية إذا تم رفع صورة جديدة
        if ($request->hasFile('profile_picture')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->profile_picture) {
                Storage::delete('public/profile_pictures/' . $user->profile_picture);
            }

            // حفظ الصورة الجديدة
            $imagePath             = $request->file('profile_picture')->store('public/profile_pictures');
            $user->profile_picture = basename($imagePath);
        }

        // ✅ حفظ التعديلات
        $user->save();

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user'    => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        // ✅ التحقق من صحة البيانات المدخلة
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        // ✅ التحقق من كلمة المرور القديمة
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'كلمة المرور الحالية غير صحيحة'], 400);
        }

        // ✅ تحديث كلمة المرور
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
    }

    public function deleteAccount(Request $request)
    {
        // ✅ التحقق من صحة البيانات
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // ✅ جلب المستخدم باستخدام `id`
        $user = User::findOrFail($request->user_id);

        // ✅ حذف جميع التوكنات الخاصة بالمستخدم (إذا كنت تستخدم Sanctum)
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // ✅ حذف صورة المستخدم من التخزين إذا كانت موجودة
        if ($user->profile_picture) {
            Storage::delete('public/profile_pictures/' . $user->profile_picture);
        }

        // ✅ حذف الحساب
        try {
            $user->delete();
        } catch (\Exception $e) {
            return response()->json([
                "message" => "فشل حذف الحساب",
                "error"   => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            "message" => "تم حذف الحساب بنجاح",
        ]);
    }

}
