<?php

namespace App\Services\LLM;

use App\Services\LLM\Contracts\LLMVisionContract;
use App\Services\LLM\Providers\AnthropicVisionProvider;
use App\Services\LLM\Providers\GeminiVisionProvider;
use App\Services\LLM\Providers\OpenAIVisionProvider;
use InvalidArgumentException;

class LLMVisionFactory
{
    public static function make(?string $provider = null): LLMVisionContract
    {
        $provider = $provider ?? config('services.llm.provider', 'gemini');

        return match ($provider) {
            'gemini' => new GeminiVisionProvider,
            'openai' => new OpenAIVisionProvider,
            'anthropic' => new AnthropicVisionProvider,
            default => throw new InvalidArgumentException("Unknown LLM provider: {$provider}"),
        };
    }
}
