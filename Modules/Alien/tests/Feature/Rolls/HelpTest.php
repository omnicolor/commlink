<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use Modules\Alien\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function json_decode;

#[Group('alien')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = new Channel([
            'system' => 'alien',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response);
        self::assertSame(
            config('app.name') . ' - Alien RPG',
            $response->attachments[0]->title
        );
        self::assertStringStartsWith(
            'I am a bot that lets you roll Alien RPG dice.',
            $response->attachments[0]->text,
        );
    }

    #[Group('discord')]
    public function testHelpDiscord(): void
    {
        $channel = new Channel([
            'system' => 'alien',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringStartsWith(
            '**' . config('app.name') . ' - Alien RPG**',
            $response,
        );
        self::assertStringContainsString(
            '`injury` - Roll 2 dice',
            $response
        );
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $channel = new Channel([
            'system' => 'alien',
            'type' => Channel::TYPE_IRC,
        ]);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringStartsWith(
            config('app.name') . ' - Alien RPG',
            $response,
        );
    }
}
