<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for generating reply suggestions.
 * 
 * Uses AI-based generation when available, falls back to templates if AI fails.
 */
class ReplyGeneratorService
{
    protected AiReplyService $aiService;

    public function __construct(AiReplyService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Fallback template responses organized by sentiment and tone.
     * Used when AI service is unavailable or fails.
     *
     * @var array<string, array<string, string>>
     */
    protected array $fallbackTemplates = [
        'positive' => [
            'friendly' => [
                'Thanks so much for your kind words! We truly appreciate your support 😊',
                'Wow, thank you! Your feedback means the world to us. We\'re thrilled you had a great experience! 🎉',
                'Thank you for the amazing review! We\'re so happy we could make your day better. Come back soon! 😄',
            ],
            'professional' => [
                'Thank you for your positive feedback. We appreciate your business and look forward to serving you again.',
                'We\'re pleased to hear about your positive experience. Thank you for choosing us and for taking the time to share your feedback.',
                'Your satisfaction is our priority. Thank you for the excellent rating and for being a valued customer.',
            ],
            'witty' => [
                'Five stars? You\'re making us blush! Thanks for being awesome and for making our day! ⭐',
                'Well, well, well... look who just made our entire team smile! Thanks for the stellar review! 🌟',
                'You\'ve officially made our week! Thanks for the amazing feedback - we\'ll try not to let it go to our heads 😎',
            ],
        ],
        'neutral' => [
            'friendly' => [
                'Thanks for taking the time to share your thoughts! We\'d love to hear more about how we can improve. Feel free to reach out anytime! 😊',
                'We appreciate your feedback! Every review helps us grow. If there\'s anything specific we can do better, we\'re all ears!',
                'Thank you for your honest feedback! We\'re always working to improve, and your input is valuable to us.',
            ],
            'professional' => [
                'Thank you for your feedback. We value your input and are committed to continuous improvement. Please contact us if you have any specific concerns.',
                'We appreciate you taking the time to share your experience. Your feedback helps us enhance our services.',
                'Thank you for your review. We take all feedback seriously and use it to improve our customer experience.',
            ],
            'witty' => [
                'Three stars? We\'ll take it! But we\'re aiming for five next time. Thanks for keeping us on our toes! 😄',
                'Thanks for the feedback! We\'re not perfect, but we\'re working on it. Challenge accepted! 💪',
                'Appreciate the honest review! We\'ll use this as fuel to get even better. Watch this space! 🚀',
            ],
        ],
        'negative' => [
            'friendly' => [
                'We\'re really sorry to hear about your experience. We\'d love to make things right! Please reach out to us directly so we can help. 😔',
                'Oh no! We hate to hear this. Your feedback is important to us, and we\'d really appreciate the chance to turn this around. Let\'s chat!',
                'We\'re sorry we let you down. We take this seriously and would love to hear more about how we can improve. Please contact us!',
            ],
            'professional' => [
                'We apologize for not meeting your expectations. We take all feedback seriously and would appreciate the opportunity to address your concerns. Please contact our customer service team.',
                'Thank you for bringing this to our attention. We sincerely apologize for the inconvenience and would like to resolve this matter. Please reach out to us at your earliest convenience.',
                'We regret that your experience did not meet our standards. We value your feedback and would like to discuss how we can improve. Please contact us directly.',
            ],
            'witty' => [
                'Ouch! That stings, but we needed to hear it. We\'re sorry we missed the mark. Let\'s fix this together - we\'re better than this! 💪',
                'Well, that\'s not the review we were hoping for, but we appreciate the honesty! We\'d love a chance to redeem ourselves. Can we chat?',
                'Thanks for keeping it real. We messed up, and we own it. But we\'re not giving up on you - let\'s make this right! 🙏',
            ],
        ],
    ];

    /**
     * Generate a reply based on review text, sentiment and tone using AI.
     * Falls back to templates if AI is unavailable.
     *
     * @param string $reviewText The review text to generate reply for
     * @param string $sentiment The sentiment: 'positive', 'neutral', or 'negative'
     * @param string $tone The tone: 'friendly', 'professional', or 'witty'
     * @return string The generated reply
     * @throws \InvalidArgumentException If sentiment or tone is invalid
     */
    public function generateReply(string $reviewText, string $sentiment, string $tone): string
    {
        $this->validateSentiment($sentiment);
        $this->validateTone($tone);

        // Try AI generation first
        if ($this->aiService->isAvailable()) {
            $aiReply = $this->aiService->generateReply($reviewText, $sentiment, $tone);
            
            if ($aiReply !== null && !empty(trim($aiReply))) {
                return $aiReply;
            }
            
            Log::warning('AI reply generation failed, falling back to templates', [
                'sentiment' => $sentiment,
                'tone' => $tone,
            ]);
        }

        // Fallback to templates
        return $this->getFallbackReply($sentiment, $tone);
    }

    /**
     * Generate all three replies (one for each tone) for a given review.
     *
     * @param string $reviewText The review text
     * @param string $sentiment The sentiment: 'positive', 'neutral', or 'negative'
     * @return array<string, string> Array with keys 'friendly', 'professional', 'witty'
     * @throws \InvalidArgumentException If sentiment is invalid
     */
    public function generateAllReplies(string $reviewText, string $sentiment): array
    {
        $this->validateSentiment($sentiment);

        return [
            'friendly' => $this->generateReply($reviewText, $sentiment, 'friendly'),
            'professional' => $this->generateReply($reviewText, $sentiment, 'professional'),
            'witty' => $this->generateReply($reviewText, $sentiment, 'witty'),
        ];
    }

    /**
     * Get a fallback template reply.
     *
     * @param string $sentiment
     * @param string $tone
     * @return string
     */
    protected function getFallbackReply(string $sentiment, string $tone): string
    {
        $templates = $this->fallbackTemplates[$sentiment][$tone];
        return $templates[array_rand($templates)];
    }

    /**
     * Get all available fallback templates for a sentiment and tone combination.
     *
     * @param string $sentiment The sentiment: 'positive', 'neutral', or 'negative'
     * @param string $tone The tone: 'friendly', 'professional', or 'witty'
     * @return array<string> Array of template strings
     * @throws \InvalidArgumentException If sentiment or tone is invalid
     */
    public function getTemplates(string $sentiment, string $tone): array
    {
        $this->validateSentiment($sentiment);
        $this->validateTone($tone);

        return $this->fallbackTemplates[$sentiment][$tone];
    }

    /**
     * Validate sentiment value.
     *
     * @param string $sentiment
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateSentiment(string $sentiment): void
    {
        $validSentiments = ['positive', 'neutral', 'negative'];
        if (!in_array($sentiment, $validSentiments, true)) {
            throw new \InvalidArgumentException("Invalid sentiment: '{$sentiment}'. Must be one of: " . implode(', ', $validSentiments));
        }
    }

    /**
     * Validate tone value.
     *
     * @param string $tone
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateTone(string $tone): void
    {
        $validTones = ['friendly', 'professional', 'witty'];
        if (!in_array($tone, $validTones, true)) {
            throw new \InvalidArgumentException("Invalid tone: '{$tone}'. Must be one of: " . implode(', ', $validTones));
        }
    }
}

