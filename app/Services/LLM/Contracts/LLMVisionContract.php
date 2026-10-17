<?php

namespace App\Services\LLM\Contracts;

interface LLMVisionContract
{
    /**
     * Send two images + a system prompt to the LLM.
     * Returns the generated text prompt string.
     */
    public function generatePrompt(
        string $baseImagePath,
        string $targetImagePath,
        string $systemPrompt
    ): string;
}
