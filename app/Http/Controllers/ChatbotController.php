<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
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
            'history' => ['nullable', 'string'],
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $categoryNames = ReportCategory::pluck('name')->implode(', ');

        $systemPrompt = "You are CivicGuard's assistant for Barangay Maimpis, helping residents file incident reports through conversation. "
            . "Your job is to gather exactly three pieces of information by asking ONE clear question at a time if missing: "
            . "1) the category (must be one of exactly: {$categoryNames}), "
            . "2) a description of what happened, "
            . "3) the location. "
            . "Keep every message short and friendly. If it sounds like an active emergency, tell them to contact barangay officials or authorities immediately instead of continuing the form. "
            . "Once you have all three pieces of information clearly, respond normally AND then on a new line append exactly this hidden marker with the collected data as JSON (the resident will not see this marker, only your normal message): "
            . "###REPORT_DATA###{\"category\":\"exact category name from the list\",\"description\":\"what happened\",\"location\":\"the location\"}###END### "
            . "Do not include the marker until you truly have all three pieces of information. Never make up information the resident hasn't given you.";

        $conversationHistory = $validated['history'] ?? '';
        $apiKey = config('services.gemini.key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt . "\n\nConversation so far:\n" . $conversationHistory . "\n\nResident's latest message: " . $validated['message']],
                    ],
                ],
            ],
        ]);

        $rawReply = $response->json('candidates.0.content.parts.0.text') ?? 'Sorry, I had trouble responding. Please try again.';

        $reportData = null;
        if (preg_match('/###REPORT_DATA###(.*?)###END###/s', $rawReply, $matches)) {
            $json = json_decode(trim($matches[1]), true);
            if ($json && isset($json['category'], $json['description'], $json['location'])) {
                $category = ReportCategory::where('name', $json['category'])->first();
                if ($category) {
                    $reportData = [
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'description' => $json['description'],
                        'location' => $json['location'],
                    ];
                }
            }
            $rawReply = trim(preg_replace('/###REPORT_DATA###.*?###END###/s', '', $rawReply));
        }

        return response()->json([
            'reply' => $rawReply,
            'session_id' => $sessionId,
            'report_data' => $reportData,
        ]);
    }
}