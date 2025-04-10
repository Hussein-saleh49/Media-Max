<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkEmailRequest;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function __construct()
    {
        $this->middleware("guest");
    }

    // Send OTP function
    public function SendOtp(LinkEmailRequest $request)
    {
        try {
            // Check if the user exists by email
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(["error" => "User not found"], 404);
            }

            // Generate a random OTP
            $otp = mt_rand(100000, 999999);

            // Store OTP and user ID in the cache
            Cache::put('otp_code', $otp, 600); // OTP valid for 10 minutes
            Cache::put('user_id', $user->id, 600); // User ID valid for 10 minutes

            // Send the OTP email
            Mail::to($request->email)->send(new OtpMail($otp));

            return response()->json([
                "message" => "OTP is sent to your email",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to send email: " . $e->getMessage(),
            ], 500);
        }
    }

    // Resend OTP function
    public function resendOtp(Request $request)
    {
        try {
            // Validate the email field
            $request->validate([
                'email' => 'required|email',
            ]);
    
            // Check if the user exists
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(["error" => "User not found"], 404);
            }
    
            // Check if the OTP was recently sent (to prevent abuse)
            if (Cache::has('otp_resend_wait')) {
                $timeLeft = Cache::get('otp_resend_wait');
                return response()->json([
                    "error" => "Please wait before requesting a new OTP.",
                    "retry_after_seconds" => $timeLeft,
                ], 429);
            }
    
            // Check if OTP exists in cache, and resend the last OTP if it does
            $otp = Cache::get('otp_code');
            if ($otp) {
                // Resend the OTP email
                Mail::to($request->email)->send(new OtpMail($otp));
    
                // Reset the resend wait timer to prevent spamming
                Cache::put('otp_resend_wait', 60, 60); // 60-second wait before resending
    
                return response()->json([
                    "message" => "A new OTP has been sent to your email.",
                ]);
            }
    
            // If no OTP found in cache, generate a new one
            $otp = mt_rand(100000, 999999);
    
            // Store the new OTP in the cache
            Cache::put('otp_code', $otp, 600); // OTP valid for 10 minutes
            Cache::put('otp_resend_wait', 60, 60); // 60-second wait before resending
    
            // Resend the OTP email
            Mail::to($request->email)->send(new OtpMail($otp));
    
            return response()->json([
                "message" => "A new OTP has been sent to your email.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Failed to resend OTP: " . $e->getMessage(),
            ], 500);
        }
    }
    

    // Verify OTP function
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_code');

        if ($cachedOtp && $cachedOtp == $request->otp) {
            // Store verification status
            Cache::put('user_verified', true, 1800); // Verified for 30 minutes

            return response()->json([
                "message" => "OTP verified successfully. You can now create a new password.",
            ]);
        }

        return response()->json([
            "error" => "Invalid OTP",
        ], 400);
    }

    // Create new password function
    public function createNewPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check verification status
        if (Cache::get('user_verified')) {
            $userId = Cache::get('user_id');
            $user = User::find($userId);

            if ($user) {
                $user->password = bcrypt($request->new_password);
                $user->save();

                // Clear cache after successful password reset
                Cache::forget('user_verified');
                Cache::forget('user_id');
                Cache::forget('otp_code');
                Cache::forget('otp_resend_wait');

                return response()->json([
                    "message" => "Password updated successfully.",
                ]);
            }

            return response()->json([
                "error" => "User not found.",
            ], 404);
        }

        return response()->json([
            "error" => "OTP not verified.",
        ], 403);
    }
}
