<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Subversion;

use App\Models\Channel;
use App\Rolls\Subversion\Help;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for getting help in a Subversion channel.
 * @group subversion
 * @medium
 */
final class HelpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting help via Slack for a channel as an unregistered user.
     * @test
     */
    public function testHelpSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'subversion',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            \sprintf('%s - Subversion', config('app.name')),
            $response->attachments[0]->title
        );
        self::assertSame(
            'Note for unregistered users:',
            $response->attachments[1]->title
        );
    }
}
