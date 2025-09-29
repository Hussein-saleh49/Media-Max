<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkEmailRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function __construct()
    {
        $this->middleware("guest");
    }

   



    public function SendOtp(Request $request)
{
    $request->validate([
        'email' => ['required', 'email', Rule::exists(User::class, 'email')],
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $otp = mt_rand(100000, 999999);

    Cache::put('otp_code_'.$user->id, $otp, 600);
    Cache::put('user_id_'.$user->id, $user->id, 600);

    // Brevo api
    $response = Http::withHeaders([
        'accept' => 'application/json',
        'api-key' => env('BREVO_API_KEY'),
        'content-type' => 'application/json',
    ])->post('https://api.brevo.com/v3/smtp/email', [
        'sender' => [
            'name' => env('MAIL_FROM_NAME'),
            'email' => env('MAIL_FROM_ADDRESS'),
        ],
        'to' => [
            [
                'email' => $request->email,
            ]
        ],
        'subject' => 'Your OTP Code',
        'htmlContent' => "<h1>رمز التحقق (OTP)</h1><p>رمز التحقق الخاص بك هو: <strong>{$otp}</strong></p><p>هذا الرمز صالح لمدة 10 دقائق.</p>"
    ]);

    if ($response->successful()) {
        return response()->json([
            'message' => 'OTP is sent to your email',
        ]);
    } else {
        return response()->json([
            'error' => 'Failed to send email: ' . $response->body(),
        ], 500);
    }
}


  



public function resendOtp(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(["error" => "المستخدم غير موجود"], 404);
        }

        // 
        if (Cache::has('otp_resend_wait_' . $user->id)) {
            return response()->json([
                "error" => "الرجاء الانتظار قبل طلب رمز تحقق جديد.",
            ], 429);
        }

        //
        $otp = Cache::get('otp_code_' . $user->id);
        if (!$otp) {
            $otp = mt_rand(100000, 999999);
            Cache::put('otp_code_' . $user->id, $otp, 600); // صالح 10 دقائق
        }

        // 
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => env('BREVO_API_KEY'),
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => env('MAIL_FROM_NAME'),
                'email' => env('MAIL_FROM_ADDRESS'),
            ],
            'to' => [
                ['email' => $user->email]
            ],
            'subject' => 'رمز التحقق الخاص بك',
            'htmlContent' => "<h1>رمز التحقق (OTP)</h1><p>رمز التحقق الخاص بك هو: <strong>{$otp}</strong></p><p>هذا الرمز صالح لمدة 10 دقائق.</p>"
        ]);

        if ($response->successful()) {
            // 
            Cache::put('otp_resend_wait_' . $user->id, true, 60);

            return response()->json([
                "message" => "تم إرسال رمز التحقق إلى بريدك الإلكتروني.",
            ]);
        } else {
            return response()->json([
                "error" => "فشل في إرسال البريد الإلكتروني: " . $response->body(),
            ], 500);
        }

    } catch (\Exception $e) {
        return response()->json([
            "error" => "حدث خطأ أثناء إعادة إرسال رمز التحقق: " . $e->getMessage(),
        ], 500);
    }
}


    // Verify OTP function
  public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json([
            "error" => "المستخدم غير موجود.",
        ], 404);
    }

    $cachedOtp = Cache::get('otp_code_' . $user->id);

    if ($cachedOtp && $cachedOtp == $request->otp) {
        Cache::put('user_verified_' . $user->id, true, 1800); // صلاحية التحقق 30 دقيقة
        Cache::put('user_id_' . $user->id, $user->id, 1800);

        return response()->json([
            "message" => "تم التحقق من رمز OTP بنجاح. يمكنك الآن إنشاء كلمة مرور جديدة.",
        ]);
    }

    return response()->json([
        "error" => "رمز التحقق غير صحيح.",
    ], 400);
}

    // Create new password function
  public function createNewPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'new_password' => 'required|min:8|confirmed',
    ]);

    // 
    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json([
            "error" => "المستخدم غير موجود.",
        ], 404);
    }

    // 
    if (Cache::get('user_verified_' . $user->id)) {
        $user->password = bcrypt($request->new_password);
        $user->save();

        // 
        Cache::forget('user_verified_' . $user->id);
        Cache::forget('user_id_' . $user->id);
        Cache::forget('otp_code_' . $user->id);
        Cache::forget('otp_resend_wait_' . $user->id);

        return response()->json([
            "message" => "تم تحديث كلمة المرور بنجاح.",
        ]);
    }

    return response()->json([
        "error" => "لم يتم التحقق من رمز OTP.",
    ], 403);
}

}
