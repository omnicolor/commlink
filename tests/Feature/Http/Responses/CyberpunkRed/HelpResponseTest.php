<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\CyberpunkRed;

use App\Http\Responses\Cyberpunkred\HelpResponse;
use App\Models\Channel;

/**
 * Tests for getting help in an Cyberpunk Red channel.
 * @covers \App\Http\Responses\Cyberpunkred\HelpResponse
 * @group cyberpunkred
 * @group slack
 * @medium
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
            '"title":"Commlink - Cyberpunk Red"',
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
        $response = \json_decode((string)$response);
        self::assertSame('ephemeral', $response->response_type);
        self::assertEquals(
            (object)[
                'title' => 'Unregistered',
                'text' => \sprintf(
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
