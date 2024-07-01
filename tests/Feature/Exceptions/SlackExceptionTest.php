<?php

declare(strict_types=1);

namespace Tests\Feature\Exceptions;

use App\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

#[Group('slack')]
#[Medium]
class SlackExceptionTest extends TestCase
{
    public function testRenderException(): void
    {
        try {
            throw new SlackException();
        } catch (SlackException $ex) {
            $response = $ex->render();
            $response = json_decode((string)$response, false);
            self::assertSame('ephemeral', $response->response_type);
            self::assertCount(1, $response->attachments);
        }
    }
}
