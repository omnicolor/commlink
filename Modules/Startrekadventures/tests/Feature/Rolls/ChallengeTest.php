<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Startrekadventures\Rolls\Challenge;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('startrekadventures')]
#[Medium]
final class ChallengeTest extends TestCase
{
    #[Group('slack')]
    public function testNoScore(): void
    {
        DiceService::shouldReceive('rollOne')->times(3)->with(6)->andReturn(3);

        $channel = Channel::factory()->make(['registered_by' => 1]);

        $response = (new Challenge('challenge 3', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_SUCCESS,
                'footer' => 'Rolls: 3 3 3',
                'text' => 'Rolled 3 challenge dice',
                'title' => 'username rolled a score of 0 without an Effect',
            ],
            $response['attachments'][0],
        );
    }

    #[Group('discord')]
    public function testWithEffect(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(6)->andReturn(6);

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

        $channel = Channel::factory()->make(['registered_by' => 3]);
        $response = (new Challenge('challenge 2 testing', 'username', $channel))
            ->forIrc();

        $expected = 'username rolled a score of 2 without an Effect for '
            . '"testing"' . PHP_EOL . 'Rolled 2 challenge dice' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }
}
