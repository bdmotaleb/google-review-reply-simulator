<?php

namespace Tests\Unit;

use App\Services\AiReplyService;
use App\Services\ReplyGeneratorService;
use Tests\TestCase;
use Mockery;

class ReplyGeneratorServiceTest extends TestCase
{
    protected ReplyGeneratorService $service;
    protected AiReplyService $aiService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock AI service to return null (will use fallback templates)
        $this->aiService = Mockery::mock(AiReplyService::class);
        $this->aiService->shouldReceive('isAvailable')->andReturn(false);
        
        $this->service = new ReplyGeneratorService($this->aiService);
    }

    /**
     * Test generating a positive friendly reply.
     */
    public function test_generate_positive_friendly_reply(): void
    {
        $reply = $this->service->generateReply('Great service!', 'positive', 'friendly');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Thanks', $reply);
    }

    /**
     * Test generating a positive professional reply.
     */
    public function test_generate_positive_professional_reply(): void
    {
        $reply = $this->service->generateReply('Excellent experience', 'positive', 'professional');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Thank you', $reply);
    }

    /**
     * Test generating a positive witty reply.
     */
    public function test_generate_positive_witty_reply(): void
    {
        $reply = $this->service->generateReply('Amazing!', 'positive', 'witty');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral friendly reply.
     */
    public function test_generate_neutral_friendly_reply(): void
    {
        $reply = $this->service->generateReply('It was okay', 'neutral', 'friendly');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral professional reply.
     */
    public function test_generate_neutral_professional_reply(): void
    {
        $reply = $this->service->generateReply('Average experience', 'neutral', 'professional');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a neutral witty reply.
     */
    public function test_generate_neutral_witty_reply(): void
    {
        $reply = $this->service->generateReply('Not bad', 'neutral', 'witty');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating a negative friendly reply.
     */
    public function test_generate_negative_friendly_reply(): void
    {
        $reply = $this->service->generateReply('Poor service', 'negative', 'friendly');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('sorry', $reply);
    }

    /**
     * Test generating a negative professional reply.
     */
    public function test_generate_negative_professional_reply(): void
    {
        $reply = $this->service->generateReply('Disappointed', 'negative', 'professional');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('apologize', $reply);
    }

    /**
     * Test generating a negative witty reply.
     */
    public function test_generate_negative_witty_reply(): void
    {
        $reply = $this->service->generateReply('Not good', 'negative', 'witty');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
    }

    /**
     * Test generating all replies for positive sentiment.
     */
    public function test_generate_all_replies_for_positive(): void
    {
        $replies = $this->service->generateAllReplies('Great service!', 'positive');
        
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
        $replies = $this->service->generateAllReplies('It was okay', 'neutral');
        
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
        $replies = $this->service->generateAllReplies('Poor service', 'negative');
        
        $this->assertIsArray($replies);
        $this->assertCount(3, $replies);
        $this->assertArrayHasKey('friendly', $replies);
        $this->assertArrayHasKey('professional', $replies);
        $this->assertArrayHasKey('witty', $replies);
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
     * Test invalid sentiment throws exception.
     */
    public function test_invalid_sentiment_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid sentiment: 'invalid'. Must be one of: positive, neutral, negative");
        
        $this->service->generateReply('Review text', 'invalid', 'friendly');
    }

    /**
     * Test invalid tone throws exception.
     */
    public function test_invalid_tone_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid tone: 'invalid'. Must be one of: friendly, professional, witty");
        
        $this->service->generateReply('Review text', 'positive', 'invalid');
    }

    /**
     * Test that fallback templates work when AI is unavailable.
     */
    public function test_fallback_templates_when_ai_unavailable(): void
    {
        $reply = $this->service->generateReply('Great service!', 'positive', 'friendly');
        
        $this->assertIsString($reply);
        $this->assertNotEmpty($reply);
        
        // Should be one of the fallback templates
        $templates = $this->service->getTemplates('positive', 'friendly');
        $this->assertContains($reply, $templates);
    }

    /**
     * Test AI service integration when available.
     */
    public function test_ai_service_integration(): void
    {
        // Mock AI service to return a reply
        $aiService = Mockery::mock(AiReplyService::class);
        $aiService->shouldReceive('isAvailable')->andReturn(true);
        $aiService->shouldReceive('generateReply')
            ->with('Great service!', 'positive', 'friendly')
            ->andReturn('AI-generated friendly reply');
        
        $service = new ReplyGeneratorService($aiService);
        $reply = $service->generateReply('Great service!', 'positive', 'friendly');
        
        $this->assertEquals('AI-generated friendly reply', $reply);
    }
}
