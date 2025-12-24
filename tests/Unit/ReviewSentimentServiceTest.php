<?php

namespace Tests\Unit;

use App\Services\ReviewSentimentService;
use Tests\TestCase;

class ReviewSentimentServiceTest extends TestCase
{
    protected ReviewSentimentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReviewSentimentService();
    }

    /**
     * Test that rating 5 returns positive sentiment.
     */
    public function test_rating_five_returns_positive(): void
    {
        $result = $this->service->determineSentiment(5);
        $this->assertEquals('positive', $result);
    }

    /**
     * Test that rating 4 returns positive sentiment.
     */
    public function test_rating_four_returns_positive(): void
    {
        $result = $this->service->determineSentiment(4);
        $this->assertEquals('positive', $result);
    }

    /**
     * Test that rating 3 returns neutral sentiment.
     */
    public function test_rating_three_returns_neutral(): void
    {
        $result = $this->service->determineSentiment(3);
        $this->assertEquals('neutral', $result);
    }

    /**
     * Test that rating 2 returns negative sentiment.
     */
    public function test_rating_two_returns_negative(): void
    {
        $result = $this->service->determineSentiment(2);
        $this->assertEquals('negative', $result);
    }

    /**
     * Test that rating 1 returns negative sentiment.
     */
    public function test_rating_one_returns_negative(): void
    {
        $result = $this->service->determineSentiment(1);
        $this->assertEquals('negative', $result);
    }

    /**
     * Test that rating 0 throws InvalidArgumentException.
     */
    public function test_rating_zero_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating must be between 1 and 5, got: 0');
        
        $this->service->determineSentiment(0);
    }

    /**
     * Test that rating 6 throws InvalidArgumentException.
     */
    public function test_rating_six_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating must be between 1 and 5, got: 6');
        
        $this->service->determineSentiment(6);
    }

    /**
     * Test that negative rating throws InvalidArgumentException.
     */
    public function test_negative_rating_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rating must be between 1 and 5, got: -1');
        
        $this->service->determineSentiment(-1);
    }

    /**
     * Test all valid ratings in a data provider.
     */
    public function test_all_valid_ratings(): void
    {
        $testCases = [
            [1, 'negative'],
            [2, 'negative'],
            [3, 'neutral'],
            [4, 'positive'],
            [5, 'positive'],
        ];

        foreach ($testCases as [$rating, $expectedSentiment]) {
            $result = $this->service->determineSentiment($rating);
            $this->assertEquals(
                $expectedSentiment,
                $result,
                "Rating {$rating} should return '{$expectedSentiment}'"
            );
        }
    }
}
