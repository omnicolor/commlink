<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Startrekadventures\Rolls\Challenge;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('startrekadventures')]
#[Medium]
final class ChallengeTest extends TestCase
{
    #[Group('slack')]
    public function testNoScore(): void
    {
        DiceService::shouldReceive('rollOne')->times(3)->with(6)->andReturn(3);

        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 1]);

        $response = new Challenge('challenge 3', 'username', $channel);
        $response = json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3 3', $response->footer);
        self::assertSame('Rolled 3 challenge dice', $response->text);
        self::assertSame(
            'username rolled a score of 0 without an Effect',
            $response->title
        );
    }

    #[Group('discord')]
    public function testWithEffect(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(6);

        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 2]);
        $response = (new Challenge('challenge 2', 'username', $channel))
            ->forDiscord();

        $expected = '**username rolled a score of 2 with an Effect**' . PHP_EOL
            . 'Rolled 2 challenge dice' . PHP_EOL
            . 'Rolls: 6 6';
        self::assertSame($expected, $response);
    }

    #[Group('irc')]
    public function testWithText(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make(['registered_by' => 3]);
        $response = (new Challenge('challenge 2 testing', 'username', $channel))
            ->forIrc();

        $expected = 'username rolled a score of 2 without an Effect for '
            . '"testing"' . PHP_EOL . 'Rolled 2 challenge dice' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }
}
