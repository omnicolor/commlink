<?php

declare(strict_types=1);

namespace Tests\Unit\Responses\Shadowrun5e;

use App\Http\Responses\Shadowrun5e\HelpResponse;

/**
 * Tests for getting help in a Shadowrun 5E channel.
 * @covers \App\Http\Responses\Shadowrun5e\HelpResponse
 * @group slack
 */
final class HelpResponseTest extends \Tests\TestCase
{
    /**
     * Test the response.
     * @test
     */
    public function testResponse(): void
    {
        $response = new HelpResponse('help', HelpResponse::HTTP_OK, []);
        self::assertStringContainsString(
            '"title":"Commlink - Shadowrun 5E"',
            (string)$response
        );
    }
}
