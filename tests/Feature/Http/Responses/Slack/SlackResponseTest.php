<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Slack\TextAttachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('slack')]
#[Medium]
final class SlackResponseTest extends TestCase
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
