<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * AI-based reply generation service.
 * 
 * Uses OpenAI API to generate contextual replies based on review content.
 */
class AiReplyService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->apiUrl = config('services.openai.api_url', 'https://api.openai.com/v1/chat/completions');
        $this->enabled = !empty($this->apiKey);
    }

    /**
     * Check if AI service is available and configured.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->enabled;
    }

    /**
     * Generate a reply using AI based on review content and tone.
     *
     * @param string $reviewText The review text
     * @param string $sentiment The sentiment (positive, neutral, negative)
     * @param string $tone The desired tone (friendly, professional, witty)
     * @return string|null Generated reply or null on failure
     */
    public function generateReply(string $reviewText, string $sentiment, string $tone): ?string
    {
        if (!$this->isAvailable()) {
            Log::warning('AI service not available - API key not configured');
            return null;
        }

        try {
            $prompt = $this->buildPrompt($reviewText, $sentiment, $tone);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => config('services.openai.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant that generates professional, appropriate replies to customer reviews for businesses.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                
                if ($reply) {
                    // Clean up the reply (remove quotes, trim whitespace)
                    $reply = trim($reply, '"\'');
                    
                    Log::debug('AI reply generated successfully', [
                        'sentiment' => $sentiment,
                        'tone' => $tone,
                        'length' => strlen($reply),
                    ]);

                    return $reply;
                }
            }

            Log::error('AI API request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('AI reply generation exception', [
                'error' => $e->getMessage(),
                'sentiment' => $sentiment,
                'tone' => $tone,
            ]);

            return null;
        }
    }

    /**
     * Build the prompt for AI generation.
     *
     * @param string $reviewText
     * @param string $sentiment
     * @param string $tone
     * @return string
     */
    protected function buildPrompt(string $reviewText, string $sentiment, string $tone): string
    {
        $toneInstructions = [
            'friendly' => 'Write in a warm, friendly, and approachable tone. Use emojis sparingly if appropriate.',
            'professional' => 'Write in a formal, professional, and business-appropriate tone. No emojis.',
            'witty' => 'Write in a clever, lighthearted, and engaging tone. Can use humor but keep it appropriate.',
        ];

        $sentimentContext = [
            'positive' => 'This is a positive review. Express gratitude and appreciation.',
            'neutral' => 'This is a neutral review. Acknowledge the feedback and show commitment to improvement.',
            'negative' => 'This is a negative review. Apologize sincerely and offer to make things right.',
        ];

        return sprintf(
            "Generate a brief, authentic reply to this customer review.\n\n" .
            "Review: %s\n\n" .
            "Sentiment: %s\n" .
            "%s\n\n" .
            "Tone: %s\n" .
            "%s\n\n" .
            "Requirements:\n" .
            "- Keep it under 150 words\n" .
            "- Be authentic and genuine\n" .
            "- Address the specific feedback\n" .
            "- Match the requested tone\n" .
            "- Do not include placeholders or generic responses\n\n" .
            "Generate only the reply text, nothing else:",
            $reviewText,
            $sentiment,
            $sentimentContext[$sentiment] ?? '',
            $tone,
            $toneInstructions[$tone] ?? ''
        );
    }
}

