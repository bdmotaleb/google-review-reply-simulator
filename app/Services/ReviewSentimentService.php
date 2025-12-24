<?php

namespace App\Services;

class ReviewSentimentService
{
    /**
     * Determine sentiment based on rating.
     *
     * @param int $rating The rating value (1-5)
     * @return string 'positive', 'neutral', or 'negative'
     * @throws \InvalidArgumentException If rating is out of valid range
     */
    public function determineSentiment(int $rating): string
    {
        $this->validateRating($rating);

        if ($rating >= 4) {
            return 'positive';
        }

        if ($rating === 3) {
            return 'neutral';
        }

        // rating <= 2
        return 'negative';
    }

    /**
     * Validate that rating is within acceptable range.
     *
     * @param int $rating
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateRating(int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException("Rating must be between 1 and 5, got: {$rating}");
        }
    }
}

