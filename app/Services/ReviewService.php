<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Service for managing reviews and their reply suggestions.
 * 
 * Handles business logic for review creation, sentiment analysis,
 * and reply generation.
 */
class ReviewService
{
    protected ReviewSentimentService $sentimentService;
    protected ReplyGeneratorService $replyGeneratorService;

    public function __construct(
        ReviewSentimentService $sentimentService,
        ReplyGeneratorService $replyGeneratorService
    ) {
        $this->sentimentService = $sentimentService;
        $this->replyGeneratorService = $replyGeneratorService;
    }

    /**
     * Create a review with sentiment detection and reply suggestions.
     *
     * @param array<string, mixed> $data
     * @return Review
     * @throws Exception
     */
    public function createReviewWithReplies(array $data): Review
    {
        try {
            return DB::transaction(function () use ($data) {
                // Determine sentiment based on rating
                $sentiment = $this->sentimentService->determineSentiment($data['rating']);

                // Create the review
                $review = Review::create([
                    'reviewer_name' => $data['reviewer_name'],
                    'rating' => $data['rating'],
                    'review_text' => $data['review_text'],
                    'sentiment' => $sentiment,
                ]);

                // Generate and save reply suggestions
                $this->generateAndSaveReplies($review, $sentiment);

                // Load relationships for response
                $review->load('replySuggestions');

                Log::info('Review created successfully', [
                    'review_id' => $review->id,
                    'sentiment' => $sentiment,
                ]);

                return $review;
            });
        } catch (Exception $e) {
            Log::error('Failed to create review', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Generate and save reply suggestions for a review.
     *
     * @param Review $review
     * @param string $sentiment
     * @return void
     * @throws Exception
     */
    protected function generateAndSaveReplies(Review $review, string $sentiment): void
    {
        try {
            $replies = $this->replyGeneratorService->generateAllReplies($sentiment);

            foreach ($replies as $tone => $replyText) {
                $review->replySuggestions()->create([
                    'tone' => $tone,
                    'reply_text' => $replyText,
                ]);
            }

            Log::debug('Reply suggestions generated', [
                'review_id' => $review->id,
                'count' => count($replies),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to generate reply suggestions', [
                'review_id' => $review->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get all reviews with their reply suggestions.
     *
     * @return Collection
     */
    public function getAllReviews(): Collection
    {
        try {
            return Review::with('replySuggestions')
                ->latest()
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to retrieve reviews', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

