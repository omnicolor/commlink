<?php

declare(strict_types=1);

namespace Modules\Legendofthefiverings4e\Tests\Feature\Rolls;

use App\Models\Channel;
use Modules\Legendofthefiverings4e\Rolls\Help;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;

use const PHP_EOL;

#[Group('legendofthefiverings4e')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $response = (new Help('help', 'username', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'I am a bot that lets you roll Legend of the Five '
                    . 'Rings dice.' . PHP_EOL
                    . '· `6 3 [text]` - Roll 6 dice, keeping 3, with optional '
                    . 'text (automatics, perception, etc)' . PHP_EOL
                    . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                    . 'optionally adding C to the result, optionally '
                    . 'describing that the roll is for "text"' . PHP_EOL,
                'title' => config('app.name') . ' - Legend of the Five Rings 4E',
            ],
            $response['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testHelpDiscord(): void
    {
        $response = (new Help('help', 'username', new Channel()))->forDiscord();
        self::assertStringStartsWith(
            '**' . config('app.name') . ' - Legend of the Five Rings 4E',
            $response,
        );
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $response = (new Help('help', 'username', new Channel()))->forIrc();
        self::assertStringStartsWith(
            config('app.name') . ' - Legend of the Five Rings 4E',
            $response,
        );
    }
}
