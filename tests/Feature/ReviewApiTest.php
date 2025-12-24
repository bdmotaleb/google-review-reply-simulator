<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\ReplySuggestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test POST /api/reviews creates a review with sentiment and reply suggestions.
     */
    public function test_store_review_creates_review_with_sentiment_and_replies(): void
    {
        $data = [
            'reviewer_name' => 'John Doe',
            'rating' => 5,
            'review_text' => 'Amazing service! Highly recommend.',
        ];

        $response = $this->postJson('/api/reviews', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reviewer_name',
                    'rating',
                    'review_text',
                    'sentiment',
                    'created_at',
                    'updated_at',
                    'reply_suggestions' => [
                        '*' => [
                            'id',
                            'tone',
                            'reply_text',
                            'created_at',
                        ],
                    ],
                ],
            ]);

        $this->assertDatabaseHas('reviews', [
            'reviewer_name' => 'John Doe',
            'rating' => 5,
            'sentiment' => 'positive',
        ]);

        $review = Review::first();
        $this->assertCount(3, $review->replySuggestions);
        $this->assertTrue($review->replySuggestions->contains('tone', 'friendly'));
        $this->assertTrue($review->replySuggestions->contains('tone', 'professional'));
        $this->assertTrue($review->replySuggestions->contains('tone', 'witty'));
    }

    /**
     * Test POST /api/reviews with rating 3 creates neutral sentiment.
     */
    public function test_store_review_with_rating_three_creates_neutral_sentiment(): void
    {
        $data = [
            'reviewer_name' => 'Jane Smith',
            'rating' => 3,
            'review_text' => 'It was okay, nothing special.',
        ];

        $response = $this->postJson('/api/reviews', $data);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'sentiment' => 'neutral',
                ],
            ]);

        $this->assertDatabaseHas('reviews', [
            'sentiment' => 'neutral',
        ]);
    }

    /**
     * Test POST /api/reviews with rating 2 creates negative sentiment.
     */
    public function test_store_review_with_rating_two_creates_negative_sentiment(): void
    {
        $data = [
            'reviewer_name' => 'Bob Johnson',
            'rating' => 2,
            'review_text' => 'Not satisfied with the service.',
        ];

        $response = $this->postJson('/api/reviews', $data);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'sentiment' => 'negative',
                ],
            ]);
    }

    /**
     * Test POST /api/reviews validation errors.
     */
    public function test_store_review_validation_errors(): void
    {
        $response = $this->postJson('/api/reviews', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reviewer_name', 'rating', 'review_text']);
    }

    /**
     * Test POST /api/reviews with invalid rating.
     */
    public function test_store_review_with_invalid_rating(): void
    {
        $data = [
            'reviewer_name' => 'Test User',
            'rating' => 6,
            'review_text' => 'Test review',
        ];

        $response = $this->postJson('/api/reviews', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /**
     * Test GET /api/reviews returns all reviews with reply suggestions.
     */
    public function test_index_reviews_returns_all_reviews(): void
    {
        // Create reviews with reply suggestions
        $review1 = Review::factory()->create([
            'reviewer_name' => 'Alice',
            'rating' => 5,
            'sentiment' => 'positive',
        ]);

        $review1->replySuggestions()->createMany([
            ['tone' => 'friendly', 'reply_text' => 'Thanks!'],
            ['tone' => 'professional', 'reply_text' => 'Thank you.'],
            ['tone' => 'witty', 'reply_text' => 'Awesome!'],
        ]);

        $review2 = Review::factory()->create([
            'reviewer_name' => 'Bob',
            'rating' => 3,
            'sentiment' => 'neutral',
        ]);

        $review2->replySuggestions()->createMany([
            ['tone' => 'friendly', 'reply_text' => 'Thanks for feedback'],
            ['tone' => 'professional', 'reply_text' => 'We appreciate it'],
            ['tone' => 'witty', 'reply_text' => 'Got it!'],
        ]);

        $response = $this->getJson('/api/reviews');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'reviewer_name',
                        'rating',
                        'review_text',
                        'sentiment',
                        'created_at',
                        'updated_at',
                        'reply_suggestions' => [
                            '*' => [
                                'id',
                                'tone',
                                'reply_text',
                                'created_at',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Test GET /api/reviews returns empty array when no reviews exist.
     */
    public function test_index_reviews_returns_empty_when_no_reviews(): void
    {
        $response = $this->getJson('/api/reviews');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [],
            ]);
    }
}
