<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginnRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    
    public function __invoke(LoginnRequest $request){
        $user = User::where("email",$request->email)->first();
        if(! $user ||! Hash::check($request->password,$user->password)){
            return response()->json([
                "message"=>"the provided credentials are incorrect"
            ],401);
        }
        
        $user->tokens()->delete();

        $token = $user->createToken("authtoken")->plainTextToken;
        return response()->json([
            "accesstoken" => $token,
            "tokentype" =>"Bareer"
        ],200);

    }
}
