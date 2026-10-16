<?php

namespace App\Services;

class AiRouter
{
    protected const OPENAI_COMPATIBLE = [
        'groq' => 'https://api.groq.com/openai/v1',
        'mistral' => 'https://api.mistral.ai/v1',
    ];

    protected ?string $lastProvider = null;

    protected array $votes = [];

    public function __construct(protected GeminiClient $gemini)
    {
    }

    /**
     * Providers that have a key set, in the order given by AI_PROVIDERS.
     */
    public function providers(): array
    {
        $providers = [];

        foreach ((array) config('services.ai.order', []) as $name) {
            $name = strtolower(trim((string) $name));

            if (isset($providers[$name])) {
                continue;
            }

            if ($name === 'gemini' && config('services.gemini.key')) {
                $providers['gemini'] = $this->gemini;
            } elseif (isset(self::OPENAI_COMPATIBLE[$name]) && config("services.{$name}.key")) {
                $providers[$name] = new OpenAiCompatibleClient(
                    $name,
                    self::OPENAI_COMPATIBLE[$name],
                    (string) config("services.{$name}.key"),
                    (string) config("services.{$name}.model"),
                    (string) config("services.{$name}.fast_model"),
                );
            }
        }

        return $providers;
    }

    public function generateText(string $prompt, int $timeout = 15, bool $fast = false): ?string
    {
        foreach ($this->providers() as $name => $provider) {
            $text = $provider->generateText($prompt, $timeout, $fast);

            if (is_string($text) && trim($text) !== '') {
                $this->lastProvider = $name;

                return $text;
            }
        }

        $this->lastProvider = null;

        return null;
    }

    public function lastProvider(): ?string
    {
        return $this->lastProvider;
    }

    public function label(string $name): string
    {
        return match ($name) {
            'gemini' => 'Gemini',
            'groq' => 'Groq',
            default => ucfirst($name),
        };
    }

    public function providerLabel(): ?string
    {
        return $this->lastProvider ? $this->label($this->lastProvider) : null;
    }

    public function voteSummary(): ?string
    {
        if ($this->votes === []) {
            return null;
        }

        return implode(', ', array_map(
            fn (string $name, string $rating) => $this->label($name).': '.$rating,
            array_keys($this->votes),
            $this->votes
        ));
    }

    /**
     * Ask every configured model for a severity and return the highest valid one.
     */
    public function severityVote(string $prompt, int $timeout = 6): ?string
    {
        $levels = ['low' => 0, 'moderate' => 1, 'high' => 2, 'critical' => 3];
        $best = null;
        $this->votes = [];

        foreach ($this->providers() as $name => $provider) {
            $text = $provider->generateText($prompt, $timeout, true);
            $word = is_string($text) ? strtolower(trim($text, " \t\n\r.!*`\"'")) : null;

            if ($word !== null && isset($levels[$word])) {
                $this->votes[$name] = $word;
            }

            if ($word !== null && isset($levels[$word]) && ($best === null || $levels[$word] > $levels[$best])) {
                $best = $word;
            }
        }

        return $best;
    }
}
