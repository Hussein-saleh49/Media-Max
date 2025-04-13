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

        // ✅ تحديد الدور (user أو driver)، وإذا لم يُرسل يكون `user` افتراضيًا
        $role = $request->input('role', 'user');

        if (!in_array($role, ['user', 'driver'])) {
            return response()->json(["message" => "الدور غير صالح. يجب أن يكون 'user' أو 'driver'"], 400);
        }

        // ✅ إنشاء المستخدم
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role,
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
            ],
            "access_token" => $token,
            "token_type" => "Bearer"
        ], 201);
    }
}

