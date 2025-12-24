<?php

namespace Tests\Unit;

use App\Services\ReplyGeneratorService;
use Tests\TestCase;

class ReplyGeneratorServiceTest extends TestCase
{
    protected ReplyGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReplyGeneratorService();
    }

    /**
     * Test generating a positive friendly reply.
     */
    public function test_generate_positive_friendly_reply(): void
    {
        $reply = $this->service->generateReply('positive', 'friendly', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Thanks', $reply);
    }

    /**
     * Test generating a positive professional reply.
     */
    public function test_generate_positive_professional_reply(): void
    {
        $reply = $this->service->generateReply('positive', 'professional', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Thank you', $reply);
    }

    /**
     * Test generating a positive witty reply.
     */
    public function test_generate_positive_witty_reply(): void
    {
        $reply = $this->service->generateReply('positive', 'witty', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral friendly reply.
     */
    public function test_generate_neutral_friendly_reply(): void
    {
        $reply = $this->service->generateReply('neutral', 'friendly', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral professional reply.
     */
    public function test_generate_neutral_professional_reply(): void
    {
        $reply = $this->service->generateReply('neutral', 'professional', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral witty reply.
     */
    public function test_generate_neutral_witty_reply(): void
    {
        $reply = $this->service->generateReply('neutral', 'witty', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a negative friendly reply.
     */
    public function test_generate_negative_friendly_reply(): void
    {
        $reply = $this->service->generateReply('negative', 'friendly', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('sorry', $reply);
    }

    /**
     * Test generating a negative professional reply.
     */
    public function test_generate_negative_professional_reply(): void
    {
        $reply = $this->service->generateReply('negative', 'professional', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('apologize', $reply);
    }

    /**
     * Test generating a negative witty reply.
     */
    public function test_generate_negative_witty_reply(): void
    {
        $reply = $this->service->generateReply('negative', 'witty', 0);
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating all replies for positive sentiment.
     */
    public function test_generate_all_replies_for_positive(): void
    {
        $replies = $this->service->generateAllReplies('positive');
        
        $this->assertIsArray($replies);
        $this->assertArrayHasKey('friendly', $replies);
        $this->assertArrayHasKey('professional', $replies);
        $this->assertArrayHasKey('witty', $replies);
        $this->assertIsString($replies['friendly']);
        $this->assertIsString($replies['professional']);
        $this->assertIsString($replies['witty']);
    }

    /**
     * Test generating all replies for neutral sentiment.
     */
    public function test_generate_all_replies_for_neutral(): void
    {
        $replies = $this->service->generateAllReplies('neutral');
        
        $this->assertIsArray($replies);
        $this->assertCount(3, $replies);
        $this->assertArrayHasKey('friendly', $replies);
        $this->assertArrayHasKey('professional', $replies);
        $this->assertArrayHasKey('witty', $replies);
    }

    /**
     * Test generating all replies for negative sentiment.
     */
    public function test_generate_all_replies_for_negative(): void
    {
        $replies = $this->service->generateAllReplies('negative');
        
        $this->assertIsArray($replies);
        $this->assertCount(3, $replies);
        $this->assertArrayHasKey('friendly', $replies);
        $this->assertArrayHasKey('professional', $replies);
        $this->assertArrayHasKey('witty', $replies);
    }

    /**
     * Test generating reply with specific index.
     */
    public function test_generate_reply_with_specific_index(): void
    {
        $reply1 = $this->service->generateReply('positive', 'friendly', 0);
        $reply2 = $this->service->generateReply('positive', 'friendly', 1);
        $reply3 = $this->service->generateReply('positive', 'friendly', 2);
        
        $this->assertNotEquals($reply1, $reply2);
        $this->assertNotEquals($reply2, $reply3);
        $this->assertNotEquals($reply1, $reply3);
    }

    /**
     * Test generating reply without index (random selection).
     */
    public function test_generate_reply_without_index_returns_random(): void
    {
        $reply = $this->service->generateReply('positive', 'friendly');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        
        // Verify it's one of the valid templates
        $templates = $this->service->getTemplates('positive', 'friendly');
        $this->assertContains($reply, $templates);
    }

    /**
     * Test getting templates for a sentiment and tone.
     */
    public function test_get_templates(): void
    {
        $templates = $this->service->getTemplates('positive', 'friendly');
        
        $this->assertIsArray($templates);
        $this->assertCount(3, $templates);
        $this->assertContains('Thanks so much for your kind words! We truly appreciate your support 😊', $templates);
    }

    /**
     * Test setting custom templates.
     */
    public function test_set_custom_templates(): void
    {
        $customTemplates = [
            'positive' => [
                'friendly' => ['Custom friendly reply 1', 'Custom friendly reply 2'],
                'professional' => ['Custom professional reply'],
                'witty' => ['Custom witty reply'],
            ],
        ];
        
        $this->service->setTemplates($customTemplates);
        
        $reply = $this->service->generateReply('positive', 'friendly', 0);
        $this->assertEquals('Custom friendly reply 1', $reply);
    }

    /**
     * Test invalid sentiment throws exception.
     */
    public function test_invalid_sentiment_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid sentiment: 'invalid'. Must be one of: positive, neutral, negative");
        
        $this->service->generateReply('invalid', 'friendly');
    }

    /**
     * Test invalid tone throws exception.
     */
    public function test_invalid_tone_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid tone: 'invalid'. Must be one of: friendly, professional, witty");
        
        $this->service->generateReply('positive', 'invalid');
    }

    /**
     * Test invalid index throws exception.
     */
    public function test_invalid_index_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Template index 10 does not exist for sentiment 'positive' and tone 'friendly'");
        
        $this->service->generateReply('positive', 'friendly', 10);
    }

    /**
     * Test that the example from requirements works.
     */
    public function test_example_positive_friendly_reply(): void
    {
        $reply = $this->service->generateReply('positive', 'friendly', 0);
        
        $this->assertEquals(
            'Thanks so much for your kind words! We truly appreciate your support 😊',
            $reply
        );
    }
}
