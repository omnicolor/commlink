<?php

declare(strict_types=1);

namespace Modules\Legendofthefiverings4e\Tests\Feature\Rolls;

use App\Models\Channel;
use Modules\Legendofthefiverings4e\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function json_decode;

#[Group('legendofthefiverings4e')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $response = (new Help('help', 'username', new Channel()))->forSlack();
        $response = json_decode((string)$response);
        self::assertSame(
            config('app.name') . ' - Legend of the Five Rings 4E',
            $response->attachments[0]->title
        );
        self::assertStringStartsWith(
            'I am a bot that lets you roll Legend of the Five Rings dice.',
            $response->attachments[0]->text,
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
