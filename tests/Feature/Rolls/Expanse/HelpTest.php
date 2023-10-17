<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Expanse;

use App\Models\Channel;
use App\Rolls\Expanse\Help;
use Tests\TestCase;

/**
 * Tests for getting help in a Channel registered to The Expanse.
 * @group discord
 * @group expanse
 * @group slack
 * @medium
 */
final class HelpTest extends TestCase
{
    /**
     * Test getting help via Slack.
     * @test
     */
    public function testHelpSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'expanse',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            'Commlink - The Expanse',
            $response->attachments[0]->title
        );
    }

    /**
     * Test getting help via Discord.
     * @test
     */
    public function testHelpDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'expanse',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            'Commlink - The Expanse',
            $response
        );
    }
}
