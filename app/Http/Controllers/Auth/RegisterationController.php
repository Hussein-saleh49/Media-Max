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
        
        $validatedData = $request->validated();

    
        $role = $request->input('role', 'patient');

    
        if (!in_array($role, ['pharmacist', 'patient'])) {
            return response()->json(["message" => "الدور غير صالح. يجب أن يكون 'pharmacist' أو 'patient'"], 400);
        }

        
        $phoneGarden = $validatedData['phone_garden'] ?? null;
        if (!$phoneGarden) {
            return response()->json(["message" => "حقل phone_garden مطلوب للتحقق"], 400);
        }

        
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role,  
            'phone_garden' => $phoneGarden,  
        ]);

        
        $token = $user->createToken("auth_token")->plainTextToken;

        
        return response()->json([
            "message" => "تم التسجيل بنجاح",
            "user" => [
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role,
                "phone_garden" => $user->phone_garden,  
            ],
            "access_token" => $token,
            "token_type" => "Bearer"
        ], 201);
    }
}
