<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    protected array $models = [
        'gemini-flash-latest',
        'gemini-flash-lite-latest',
    ];

    public function generateText(string $prompt, int $timeout = 15, bool $fast = false): ?string
    {
        $apiKey = config('services.gemini.key');

        if (! $apiKey) {
            return null;
        }

        foreach ($fast ? ['gemini-flash-lite-latest', 'gemini-flash-latest'] : $this->models as $model) {
            for ($attempt = 1; $attempt <= ($fast ? 1 : 2); $attempt++) {
                try {
                    $response = Http::timeout($timeout)
                        ->withHeaders(['x-goog-api-key' => $apiKey])
                        ->post(
                            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
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

                    Log::warning('Gemini request failed.', [
                        'model' => $model,
                        'status' => $response->status(),
                        'fast_request' => $fast,
                    ]);
                    if ($response->status() === 503 && $attempt === 1) {
                        sleep(1);

                        continue;
                    }

                    break;
                } catch (\Throwable $exception) {
                    Log::warning('Gemini request failed before receiving a response.', [
                        'model' => $model,
                        'exception' => $exception::class,
                    ]);

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
