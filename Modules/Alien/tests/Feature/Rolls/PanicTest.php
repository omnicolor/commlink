<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Alien\Rolls\Panic;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

#[Group('alien')]
#[Medium]
final class PanicTest extends TestCase
{
    #[Group('slack')]
    public function testSlack(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(1);
        $response = (new Panic('', 'user', new Channel()))->forSlack();
        $attachment = json_decode((string)$response)->attachments[0];

        self::assertSame(
            'user rolled 1+0=1 on the panic roll table',
            $attachment->title,
        );
        self::assertStringStartsWith('KEEPING IT', $attachment->text);
        self::assertSame('good', $attachment->color);
    }

    #[Group('discord')]
    public function testDiscordWithStress(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(1);
        $response = (new Panic('panic 6', 'user', new Channel()))->forDiscord();

        self::assertStringStartsWith(
            '**user rolled 1+6=7 on the panic roll table**',
            $response
        );
        self::assertStringContainsString('NERVOUS TWITCH', $response);
    }

    #[Group('slack')]
    public function testSlackFailed(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(6);
        $response = (new Panic('panic 2', 'user', new Channel()))->forSlack();
        $attachment = json_decode((string)$response)->attachments[0];

        self::assertSame(
            'user rolled 6+2=8 on the panic roll table',
            $attachment->title,
        );
        self::assertStringStartsWith('TREMBLE', $attachment->text);
        self::assertSame('danger', $attachment->color);
    }

    public function testIrcAndRemainingValues(): void
    {
        DiceService::shouldReceive('rollOne')->with(6)->andReturn(6);
        $response = (new Panic('panic 3', 'user', new Channel()))->forIrc();
        self::assertStringStartsWith(
            'user rolled 6+3=9 on the panic roll table',
            $response
        );
        self::assertStringContainsString('DROP ITEM', $response);
        $response = (new Panic('panic 4', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('FREEZE', $response);
        $response = (new Panic('panic 5', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('SEEK COVER', $response);
        $response = (new Panic('panic 6', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('SCREAM', $response);
        $response = (new Panic('panic 7', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('FLEE', $response);
        $response = (new Panic('panic 8', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('BERSERK', $response);
        $response = (new Panic('panic 9', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('CATATONIC', $response);
    }
}
