<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function widget()
    {
        return view('chatbot.widget');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'session_id' => ['nullable', 'string'],
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();

        $systemPrompt = "You are CivicGuard's assistant for Barangay Maimpis. "
            . "Help residents describe incidents clearly (what happened, where, when, who's involved) "
            . "so they can file an accurate report. Keep answers short and friendly. "
            . "If it sounds like an emergency in progress, tell them to contact barangay officials or authorities immediately.";

        $apiKey = config('services.gemini.key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nResident's message: " . $validated['message']],
                    ],
                ],
            ],
        ]);

        $reply = $response->json('candidates.0.content.parts.0.text') ?? 'Sorry, I had trouble responding. Please try again.';

        return response()->json([
            'reply' => $reply,
            'session_id' => $sessionId,
        ]);
    }
}