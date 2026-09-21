<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiClient
{
    protected array $models = [
        'gemini-flash-latest',
        'gemini-2.5-flash',
        'gemini-flash-lite-latest',
    ];

    public function generateText(string $prompt, int $timeout = 15): ?string
    {
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return null;
        }

        foreach ($this->models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout($timeout)->post(
                        "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                        [
                            'contents' => [
                                ['role' => 'user', 'parts' => [['text' => $prompt]]],
                            ],
                        ]
                    );

                    if ($response->successful()) {
                        $text = $response->json('candidates.0.content.parts.0.text');
                        if ($text) {
                            return $text;
                        }
                    }

                    if ($response->status() === 503 && $attempt === 1) {
                        sleep(1);
                        continue;
                    }

                    break;
                } catch (\Throwable $exception) {
                    if ($attempt === 1) {
                        sleep(1);
                        continue;
                    }
                    break;
                }
            }
        }

        return null;
    }
}
