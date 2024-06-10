<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Expanse;

use App\Models\Channel;
use App\Rolls\Expanse\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('expanse')]
#[Medium]
final class HelpTest extends TestCase
{
    /**
     * Test getting help via Slack.
     */
    #[Group('slack')]
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
     */
    #[Group('discord')]
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

    /**
     * Test getting help via IRC.
     */
    #[Group('irc')]
    public function testHelpIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringContainsString(
            'Commlink - The Expanse',
            $response
        );
    }
}
