<?php

namespace App\Http\Controllers;
use illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller{

    public function __invoke(Request $request)
    {
        $response = Http::withOptions(['verify' => false])
        ->withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer " . env("CHAT_GPT_KEY"),
        ])
        ->post("https://api.openai.com/v1/chat/completions", [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                ["role" => "system", "content" => "You are a helpful assistant."],
                ["role" => "user", "content" => $request->input('message')],
            ],
            "max_tokens" => 50,
            "temperature" => 0.7,
        ]);
    
    
        return response()->json($response->json());
    }
}    