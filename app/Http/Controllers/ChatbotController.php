<?php

namespace App\Http\Controllers;

use App\Models\ReportCategory;
use App\Services\AiRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function send(Request $request, AiRouter $ai)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'session_id' => ['nullable', 'string'],
            'history' => ['nullable', 'string'],
            'emergency_ack' => ['nullable', 'boolean'],
        ]);

        $sessionId = $validated['session_id'] ?? (string) Str::uuid();
        $emergencyAcknowledged = $request->boolean('emergency_ack');
        $conversationHistory = mb_substr($validated['history'] ?? '', -6000);
        $residentMessages = preg_match_all('/(?:^|\n)Resident:\s*(.*)/u', $conversationHistory, $residentTurns)
            ? implode("\n", $residentTurns[1])."\n"
            : '';
        $latestMessage = trim($validated['message']);
        $languageHint = $this->isTagalog($latestMessage)
            ? 'Tagalog or Taglish'
            : ($this->isShortReply($latestMessage) && ! $this->isEnglish($latestMessage) && $this->isTagalog($residentMessages) ? 'Tagalog or Taglish' : 'English');

        // Fixed emergency guidance, shown instantly (no AI call) the first time a message sounds urgent.
        if (! $emergencyAcknowledged && $this->looksLikeEmergency($validated['message'])) {
            return response()->json([
                'reply' => $this->isTagalog($validated['message'])
                    ? 'Maaaring emergency ito. Kung may taong nasa panganib ngayon, tumawag sa 911 o makipag-ugnayan agad sa mga opisyal ng barangay. Huwag hintayin ang app.'
                    : __('This may be an emergency. If someone is in danger right now, call 911 or contact the barangay officials immediately. Please do not wait for the app.'),
                'session_id' => $sessionId,
                'report_data' => null,
                'emergency' => true,
            ]);
        }
        $categoryNames = ReportCategory::pluck('name')->implode(', ');
        $faqContext = "Frequently asked questions and approved answers:\n"
            ."- How do I report an incident? Choose a category, describe what happened, provide the location, and optionally attach a photo.\n"
            ."- How can I track my report? Open My Reports after signing in to see whether it is Pending, In Progress, or Resolved.\n"
            ."- What do the urgency levels mean? Low, Moderate, High, and Critical help officials prioritize responses.\n"
            ."- What should I do during an emergency? Contact barangay officials or emergency authorities immediately; do not wait for the app.\n"
            ."- How are curfew violations recorded? Authorized tanods log the minor's details, guardian information, apprehension location, prior violations, and notes.";

        $systemPrompt = "You are CivicGuard's AI chatbot, helping residents file incident reports through conversation. "
            .'The resident\'s latest message is classified as '.$languageHint.'. Reply in that language for this turn. If it is English, answer in English even when the incident category is Dispute or the conversation history contains Tagalog examples. If the latest message is a short answer with no clear language clues, continue in the language used in the resident\'s previous messages. Switch languages only when the resident does. Translate conversational replies and FAQ answers, but keep report category names exactly as listed and keep the hidden report JSON category value in its exact listed form. '
            .'Use a calm, natural, respectful, concise tone in both English and Tagalog. Avoid dramatic reactions, canned empathy, filler, and overly formal or flowery phrasing; use "po" naturally without repeating it in every sentence. When mentioning a category to the resident, write its exact name in bold with quotation marks, for example **"Dispute"**. Do not escape the asterisks or leave bold markers unmatched. '
            .'Your job is to gather exactly three pieces of information by asking ONE clear question at a time if missing: '
            ."1) the category (must be one of exactly: {$categoryNames}), "
            .'2) a description of what happened, '
            .'3) the location. '
            .'Infer the category from the meaning of the resident\'s description in English, Tagalog, or Taglish; do not ask the resident to name a category when the incident clearly identifies one. For example, a fight or argument between people, including "may nag-away dito", "nag-aaway sila", or "may nag-away sa amin", is Dispute. Loud neighbors, "maingay dito", "may nagingay dito", "may nag-iingay dito", loud music, or repeated noise is Noise Complaint. Loud public commotion or people causing a broader public disturbance is Public Disturbance; a roaming/stray dog or cat is Stray Animal; a curfew violation is Curfew Violation; damaging property or graffiti is Vandalism. Use only categories that exist in the list above, keep the exact English category name in the hidden JSON, and ask one short clarifying question if the meaning is genuinely unclear. Treat the category as a suggestion for the resident to review. '
            .'Keep every message short and friendly. For example, after a resident describes a fight in English, say "Thanks for the details. I’ll classify this as **"Dispute"**. Where exactly did it happen?". If they describe it in Tagalog, reply naturally in Tagalog, such as "Salamat po sa detalye. Iuuri ko ito bilang **"Dispute"**. Saan eksaktong nangyari ang insidente?" If it sounds like an active emergency, tell them to contact barangay officials or authorities immediately instead of continuing the form. '
            .'Once you have all three required pieces of information clearly, tell the resident in their language that the report details are ready and ask whether they would like to attach an optional photo. Tell them they can add it with the upload button in the review card. Do not wait for their answer about the photo; immediately after that short message, on a new line append exactly this hidden marker with the collected data as JSON (the resident will not see this marker, only your normal message): '
            .'###REPORT_DATA###{"category":"exact category name from the list","description":"what happened","location":"the location"}###END### '
            ."Do not include the marker until you truly have all three pieces of information. Never make up information the resident hasn't given you. "
            ."Answer FAQ questions using this approved context:\n{$faqContext}";

        if ($emergencyAcknowledged) {
            $systemPrompt .= "\n\nThe resident has confirmed they are safe and wants to continue filing a report. "
                .'Do not repeat the emergency warning. Continue gathering the three pieces of information.';
        }

        $tagalogResponse = $languageHint !== 'English';

        $rawReply = $ai->generateText(
            $systemPrompt."\n\nConversation so far:\n".$conversationHistory."\n\nResident's latest message: ".$validated['message'],
            8,
            true
        );

        $modelNote = $this->modelNote($ai, (bool) $rawReply);
        $rawReply = $rawReply ?: $this->faqFallback($validated['message'], $tagalogResponse);

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

        // Never show a half-finished hidden marker to the resident.
        $rawReply = trim(preg_replace('/###REPORT_DATA###.*$/s', '', $rawReply));

        return response()->json([
            'reply' => $rawReply,
            'session_id' => $sessionId,
            'report_data' => $reportData,
            'model_note' => $modelNote,
        ]);
    }

    private function modelNote(AiRouter $ai, bool $answeredByAi): ?string
    {
        if (! $answeredByAi) {
            return __('Built-in answer (AI unavailable)');
        }

        try {
            $label = $ai->providerLabel();
        } catch (\Throwable) {
            return null;
        }

        return $label ? __('Answered by :model', ['model' => $label]) : null;
    }

    private function looksLikeEmergency(string $message): bool
    {
        $pattern = '/\b(gun|gunshot|firearm|knife|machete|weapon|stab|stabbed|stabbing|shot|shooting|fire|unconscious|not breathing|bleeding|kidnap|kidnapped|dying|attacked|baril|binaril|patalim|kutsilyo|sinaksak|sunog|walang malay|dumudugo)\b/iu';

        return (bool) preg_match($pattern, $message);
    }

    private function faqFallback(string $message, bool $tagalog = false): string
    {
        $message = strtolower($message);

        if (str_contains($message, 'emergency') || str_contains($message, 'urgent')) {
            return $tagalog
                ? 'Kung may agarang emergency, makipag-ugnayan agad sa mga opisyal ng barangay o awtoridad. Huwag hintayin ang app.'
                : 'For an active emergency, contact barangay officials or emergency authorities immediately. Do not wait for the app.';
        }

        if (str_contains($message, 'status') || str_contains($message, 'track')) {
            return $tagalog
                ? 'Mag-sign in at buksan ang My Reports para makita kung ang ulat ay Pending, In Progress, o Resolved.'
                : 'Sign in and open My Reports to track a report as Pending, In Progress, or Resolved.';
        }

        if (str_contains($message, 'severity') || str_contains($message, 'urgency') || str_contains($message, 'priority')) {
            return $tagalog
                ? 'Ginagamit ang Low, Moderate, High, at Critical para matulungan ang mga opisyal na unahin ang mga ulat.'
                : 'Reports are prioritized as Low, Moderate, High, or Critical so officials can respond appropriately.';
        }

        if (str_contains($message, 'curfew')) {
            return $tagalog
                ? 'Itinatala ng mga awtorisadong tanod ang detalye ng menor de edad, impormasyon ng magulang o tagapag-alaga, lokasyon, mga naunang paglabag, at mga tala.'
                : 'Authorized tanods record curfew violations with the minor\'s details, guardian information, location, prior violations, and notes.';
        }

        if (str_contains($message, 'how do i report') || str_contains($message, 'submit a report')) {
            return $tagalog
                ? 'Pumili ng kategorya, ilarawan ang nangyari, ibigay ang lokasyon, at maglakip ng larawan kung nais. Gagabayan kita sa mga detalyeng ito nang paisa-isa.'
                : 'Choose a category, describe what happened, provide the location, and optionally attach a photo. I can guide you through those details one at a time.';
        }

        return $tagalog
            ? 'Matutulungan kitang mag-ulat ng insidente. Sabihin ang kategorya, kung ano ang nangyari, at kung saan ito naganap.'
            : 'I can help you file an incident report. Tell me the category, what happened, and where it occurred.';
    }

    private function isTagalog(string $text): bool
    {
        $text = mb_strtolower($text);
        // Common incident phrases can be clearly Tagalog even when they contain
        // only one of the short function words counted below (for example,
        // "nagaway mga bata"). Recognize these before applying the broader
        // word-count heuristic.
        if (preg_match('/\b(?:nag[- ]?away|nag-aaway|away sila|may away|mga bata|bata|laruan|laro|maingay|nagingay|nag-iingay|ingay|kapitbahay)\b/u', $text)) {
            return true;
        }

        $distinctiveWords = preg_match_all('/\b(?:opo|hindi|wala|saan|bakit|paano|kailan|nangyari|puwede|pwede|salamat|kumusta|tulong|kailangan|nasaan|ano)\b/u', $text);
        $commonWords = preg_match_all('/\b(?:ako|ko|mo|siya|kami|tayo|kayo|sila|ang|ng|mga|sa|po|may|dito|doon|ito|iyan|namin|natin|gusto|paki)\b/u', $text);

        return $distinctiveWords > 0 || $commonWords >= 2;
    }

    private function isShortReply(string $text): bool
    {
        return str_word_count($text) <= 3;
    }

    private function isEnglish(string $text): bool
    {
        return (bool) preg_match('/\b(?:the|a|an|is|are|was|were|have|has|do|does|did|what|where|when|why|how|who|which|and|or|but|with|from|for|to|in|on|at|of|my|me|you|your|we|they|he|she|it|there|here|yes|no|okay|ok|please|kids?|fought|fight|fighting|loud|noise|neighbors?|someone|happened|report|incident)\b/iu', $text);
    }
}
