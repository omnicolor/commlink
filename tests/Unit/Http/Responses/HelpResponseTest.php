<?php

declare(strict_types=1);

namespace Tests\Unit\Responses;

use App\Http\Responses\HelpResponse;

/**
 * Tests for unregistered HelpResponses.
 * @group slack
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Test the three titles for a `/roll help` command in an unregistered
     * channel.
     * @test
     */
    public function testTitles(): void
    {
        $response = new HelpResponse();
        $text = (string)$response;
        $response = json_decode($text);
        self::assertSame('ephemeral', $response->response_type);
        self::assertCount(3, $response->attachments);
        self::assertSame(
            sprintf('About %s', config('app.name')),
            $response->attachments[0]->title
        );
        self::assertSame('Supported Systems', $response->attachments[1]->title);
        self::assertSame(
            'Commands For Unregistered Channels',
            $response->attachments[2]->title
        );
    }
}
