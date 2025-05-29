<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use Modules\Expanse\Rolls\Help;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('expanse')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'expanse',
            'type' => ChannelType::Slack,
        ]);
        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'Commlink is a Slack/Discord bot that lets you roll '
                    . 'dice for The Expanse.' . PHP_EOL
                    . '· `4 [text]` - Roll 3d6 dice adding 4 to the result '
                    . 'with optional text (automatics, perception, etc)'
                    . PHP_EOL,
                'title' => 'Commlink - The Expanse',
            ],
            $response['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testHelpDiscord(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'expanse',
            'type' => ChannelType::Discord,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            'Commlink - The Expanse',
            $response,
        );
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringContainsString(
            'Commlink - The Expanse',
            $response,
        );
    }
}
