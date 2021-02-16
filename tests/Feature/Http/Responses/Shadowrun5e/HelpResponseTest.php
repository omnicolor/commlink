<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Shadowrun5e;

use App\Http\Responses\Shadowrun5e\HelpResponse;
use App\Models\Slack\Channel;

/**
 * Tests for getting help in a Shadowrun 5E channel.
 * @covers \App\Http\Responses\Shadowrun5e\HelpResponse
 * @group slack
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Test the response without a channel.
     * @test
     */
    public function testResponse(): void
    {
        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            new Channel()
        );
        self::assertStringContainsString(
            '"title":"Commlink - Shadowrun 5E"',
            (string)$response
        );
    }

    /**
     * Test the response with a channel that's not linked with a character.
     * @test
     */
    public function testResponseUnregistered(): void
    {
        $response = new HelpResponse(
            'help',
            HelpResponse::HTTP_OK,
            [],
            new Channel()
        );
        $response = json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            (object)[
                'title' => 'Unregistered',
                'text' => sprintf(
                    'It doesn\'t look like you\'ve linked a character here. If '
                        . 'you\'ve already built a character in <%s|Commlink>, '
                        . 'type `/roll link <characterId>` to connect your '
                        . 'character here.',
                    config('app.url')
                ),
                'color' => HelpResponse::COLOR_INFO,
            ],
            $response->attachments[1]
        );
    }
}
