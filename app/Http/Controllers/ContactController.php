<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    // ✅ إرسال رسالة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|min:5'
        ]);

        $contactMessage = ContactMessage::create([
            'name' => $request->name,
            'message' => $request->message
        ]);

        return response()->json([
            'message' => 'تم إرسال الرسالة بنجاح!',
            'contact_message' => $contactMessage
        ], 201);
    }

    // ✅ جلب جميع الرسائل (للمسؤول فقط)
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return response()->json($messages);
    }
}
