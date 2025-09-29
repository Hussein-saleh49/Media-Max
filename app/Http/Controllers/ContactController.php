<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    

     public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'message' => 'required|string',
        ]);

        $data = [
            'sender' => [
                'name' => 'Media_Max',
                'email' => 'husseinarfat49@gmail.com',
            ],
            'to' => [
                [
                    'email' => env('BREVO_TO_EMAIL'),
                    'name' => 'Admin',
                ]
            ],
            'subject' => 'New Message from User',
            'htmlContent' => "
                <h3>New message from: {$request->name}</h3>
                <p>{$request->message}</p>
            ",
        ];

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $data);

        if ($response->successful()) {
            return response()->json(['message' => 'Message sent successfully.']);
        } else {
            return response()->json([
                'message' => 'Failed to send message.',
                'error' => $response->body()
            ], 500);
        }
    }
}
