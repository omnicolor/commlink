<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;

/**
 * Tests for the Slack Exception.
 * @group exception
 * @medium
 */
class SlackExceptionTest extends \Tests\TestCase
{
    /**
     * Test the exception's render method.
     * @test
     */
    public function testRenderException(): void
    {
        try {
            throw new SlackException();
        } catch (SlackException $ex) {
            $response = $ex->render();
            self::assertInstanceOf(SlackResponse::class, $response);
            $response = \json_decode((string)$response, false);
            self::assertSame('ephemeral', $response->response_type);
            self::assertCount(1, $response->attachments);
        }
    }
}
