<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Alien\Rolls\Trauma;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('alien')]
#[Medium]
final class TraumaTest extends TestCase
{
    #[Group('slack')]
    public function testSlack(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(1);
        $response = (new Trauma('', 'user', new Channel()))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('color', $response['attachments'][0]);
        self::assertArrayHasKey('text', $response['attachments'][0]);
        self::assertSame(
            'user rolled 1 on the permanent mental trauma table',
            $response['attachments'][0]['title'],
        );
        self::assertStringStartsWith(
            'PHOBIA',
            $response['attachments'][0]['text'],
        );
        self::assertSame('danger', $response['attachments'][0]['color']);
    }

    #[Group('discord')]
    public function testDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(2);
        $response = (new Trauma('', 'user', new Channel()))->forDiscord();

        self::assertStringStartsWith(
            '**user rolled 2 on the permanent mental trauma table**',
            $response,
        );
        self::assertStringContainsString('ALCOHOLISM', $response);
    }

    #[Group('irc')]
    public function testIrcAndRemainingOptions(): void
    {
        DiceService::shouldReceive('rollOne')->with(6)->andReturn(3, 4, 5, 6);
        $response = (new Trauma('', 'user', new Channel()))->forIrc();
        self::assertStringStartsWith(
            'user rolled 3 on the permanent mental trauma table',
            $response,
        );
        self::assertStringContainsString('NIGHTMARES', $response);

        $response = (new Trauma('', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('DEPRESSION', $response);
        $response = (new Trauma('', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('DRUG USE', $response);
        $response = (new Trauma('', 'user', new Channel()))->forIrc();
        self::assertStringContainsString('AMNESIA', $response);
    }
}
