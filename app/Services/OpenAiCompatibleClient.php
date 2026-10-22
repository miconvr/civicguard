<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiCompatibleClient
{
    public function __construct(
        protected string $name,
        protected string $baseUrl,
        protected string $key,
        protected string $model,
        protected string $fastModel,
    ) {}

    public function generateText(string $prompt, int $timeout = 15, bool $fast = false): ?string
    {
        try {
            $response = Http::timeout($timeout)
                ->withToken($this->key)
                ->acceptJson()
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                    'model' => $fast && $this->fastModel !== '' ? $this->fastModel : $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->successful()) {
                $text = $response->json('choices.0.message.content');

                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }

            Log::warning('AI provider request failed.', [
                'provider' => $this->name,
                'status' => $response->status(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('AI provider request failed before receiving a response.', [
                'provider' => $this->name,
                'exception' => $exception::class,
            ]);
        }

        return null;
    }
}
