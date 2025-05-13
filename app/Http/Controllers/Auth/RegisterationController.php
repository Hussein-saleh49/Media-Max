<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterationController extends Controller
{
    public function register(RegisterationRequest $request)
    {
        // ✅ التحقق من صحة البيانات المدخلة
        $validatedData = $request->validated();

        // ✅ تحديد الدور (pharmacist أو patient)، وإذا لم يُرسل يكون `patient` افتراضيًا
        $role = $request->input('role', 'patient');

        // ✅ التحقق من صلاحية الدور المُرسل
        if (!in_array($role, ['pharmacist', 'patient'])) {
            return response()->json(["message" => "الدور غير صالح. يجب أن يكون 'pharmacist' أو 'patient'"], 400);
        }

        // ✅ التحقق من وجود حقل phone_garden
        $phoneGarden = $validatedData['phone_garden'] ?? null;
        if (!$phoneGarden) {
            return response()->json(["message" => "حقل phone_garden مطلوب للتحقق"], 400);
        }

        // ✅ إنشاء المستخدم
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role,  // إضافة الدور هنا
            'phone_garden' => $phoneGarden,  // إضافة حقل phone_garden
        ]);

        // ✅ إنشاء التوكن
        $token = $user->createToken("auth_token")->plainTextToken;

        // ✅ إرسال الاستجابة
        return response()->json([
            "message" => "تم التسجيل بنجاح",
            "user" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "phone_garden" => $user->phone_garden,  // إضافة phone_garden في الاستجابة
            ],
            "access_token" => $token,
            "token_type" => "Bearer"
        ], 201);
    }
}
