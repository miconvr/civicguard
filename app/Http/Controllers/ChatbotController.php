<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
use Illuminate\Http\Request;
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
        $faqContext = "Frequently asked questions and approved answers:\n"
            . "- How do I report an incident? Choose a category, describe what happened, provide the location, and optionally attach a photo.\n"
            . "- How can I track my report? Open My Reports after signing in to see whether it is Pending, In Progress, or Resolved.\n"
            . "- What do the urgency levels mean? Low, Moderate, High, and Critical help officials prioritize responses.\n"
            . "- What should I do during an emergency? Contact barangay officials or emergency authorities immediately; do not wait for the app.\n"
            . "- How are curfew violations recorded? Authorized tanods log the minor's details, guardian information, apprehension location, prior violations, and notes.";

        $systemPrompt = "You are CivicGuard's assistant for Barangay Maimpis, helping residents file incident reports through conversation. "
            . "Your job is to gather exactly three pieces of information by asking ONE clear question at a time if missing: "
            . "1) the category (must be one of exactly: {$categoryNames}), "
            . "2) a description of what happened, "
            . "3) the location. "
            . "Keep every message short and friendly. If it sounds like an active emergency, tell them to contact barangay officials or authorities immediately instead of continuing the form. "
            . "Once you have all three pieces of information clearly, respond normally AND then on a new line append exactly this hidden marker with the collected data as JSON (the resident will not see this marker, only your normal message): "
            . "###REPORT_DATA###{\"category\":\"exact category name from the list\",\"description\":\"what happened\",\"location\":\"the location\"}###END### "
            . "Do not include the marker until you truly have all three pieces of information. Never make up information the resident hasn't given you. "
            . "Answer FAQ questions using this approved context:\n{$faqContext}";

        $conversationHistory = $validated['history'] ?? '';
        $apiKey = config('services.gemini.key');

        $rawReply = null;

        if ($apiKey) {
            try {
                $response = Http::timeout(8)->withHeaders([
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

                $rawReply = $response->json('candidates.0.content.parts.0.text');
            } catch (\Throwable $exception) {
                $rawReply = null;
            }
        }

        $rawReply = $rawReply ?: $this->faqFallback($validated['message']);

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

    private function faqFallback(string $message): string
    {
        $message = strtolower($message);

        if (str_contains($message, 'emergency') || str_contains($message, 'urgent')) {
            return 'For an active emergency, contact barangay officials or emergency authorities immediately. Do not wait for the app.';
        }

        if (str_contains($message, 'status') || str_contains($message, 'track')) {
            return 'Sign in and open My Reports to track a report as Pending, In Progress, or Resolved.';
        }

        if (str_contains($message, 'severity') || str_contains($message, 'urgency') || str_contains($message, 'priority')) {
            return 'Reports are prioritized as Low, Moderate, High, or Critical so officials can respond appropriately.';
        }

        if (str_contains($message, 'curfew')) {
            return 'Authorized tanods record curfew violations with the minor\'s details, guardian information, location, prior violations, and notes.';
        }

        if (str_contains($message, 'how do i report') || str_contains($message, 'submit a report')) {
            return 'Choose a category, describe what happened, provide the location, and optionally attach a photo. I can guide you through those details one at a time.';
        }

        return 'I can help you file an incident report. Tell me the category, what happened, and where it occurred.';
    }
}