<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses;

use App\Http\Responses\SlackResponse;
use App\Models\Slack\TextAttachment;

/**
 * Tests for the SlackResponse class.
 * @group slack
 * @medium
 */
final class SlackResponseTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var SlackResponse
     */
    protected SlackResponse $response;

    /**
     * Set up the subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->response = new SlackResponse();
    }

    /**
     * Test the most basic Slack Response.
     * @test
     */
    public function testBaseSlackResponse(): void
    {
        self::assertSame(
            '{"response_type":"ephemeral"}',
            (string)$this->response
        );
    }

    /**
     * Test setting things in the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $response = new SlackResponse(
            'Content',
            SlackResponse::HTTP_NOT_FOUND,
            ['Content-Type' => 'Your Mom']
        );
        self::assertSame(
            '{"response_type":"ephemeral"}',
            (string)$this->response
        );
    }

    /**
     * Test changing a Slack Response to go to the channel.
     * @test
     */
    public function testToChannel(): void
    {
        $this->response->sendToChannel();
        self::assertSame(
            '{"response_type":"in_channel"}',
            (string)$this->response
        );
    }

    /**
     * Test setting some text to send with the response.
     * @test
     */
    public function testWithText(): void
    {
        $this->response->setText('Test string');
        self::assertSame(
            '{"response_type":"ephemeral","text":"Test string"}',
            (string)$this->response
        );
    }

    /**
     * Test adding an attachment to the response.
     * @test
     */
    public function testWithAttachment(): void
    {
        $this->response->addAttachment(new TextAttachment(
            'Title',
            'Attachment Text',
            TextAttachment::COLOR_DANGER
        ));
        self::assertSame(
            '{"response_type":"ephemeral","attachments":[{"color":"danger",'
                . '"text":"Attachment Text","title":"Title"}]}',
            (string)$this->response
        );
    }

    /**
     * Test replacing the original message with this response.
     * @test
     */
    public function testReplaceOriginal(): void
    {
        $this->response->replaceOriginal();
        self::assertSame(
            '{"response_type":"ephemeral","replace_original":true}',
            (string)$this->response
        );
    }

    /**
     * Test deleting the original message.
     * @test
     */
    public function testDeleteOriginal(): void
    {
        $this->response->deleteOriginal();
        self::assertSame(
            '{"response_type":"ephemeral","delete_original":true}',
            (string)$this->response
        );
    }
}
