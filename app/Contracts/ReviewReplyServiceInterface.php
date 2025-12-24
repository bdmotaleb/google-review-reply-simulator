<?php

namespace App\Contracts;

use App\Models\Review;

/**
 * Interface for review reply generation services.
 * 
 * This interface allows for different implementations:
 * - Template-based replies (current implementation)
 * - Google AI/ML API integration (future implementation)
 * - Other third-party services
 */
interface ReviewReplyServiceInterface
{
    /**
     * Generate reply suggestions for a review.
     *
     * @param Review $review The review to generate replies for
     * @return array<string, string> Array of replies keyed by tone (friendly, professional, witty)
     */
    public function generateReplies(Review $review): array;
}

