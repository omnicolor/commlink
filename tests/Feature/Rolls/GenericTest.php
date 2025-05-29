<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Generic;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Contexts\PlainText;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Sections\Text;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('discord')]
#[Group('slack')]
#[Medium]
final class GenericTest extends TestCase
{
    /**
     * Test a simple roll with no addition or subtraction.
     */
    public function testSimple(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 2, 2]);

        $channel = Channel::factory()->make();
        $response = (new Generic('3d6', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertEquals(
            (new Header('username rolled 6'))->jsonSerialize(),
            $response['blocks'][0],
        );
        self::assertEquals(
            (new Text('Rolling: 3d6 = [2+2+2] = 6'))->jsonSerialize(),
            $response['blocks'][1],
        );
        self::assertEquals(
            (new PlainText('Rolls: 2, 2, 2'))->jsonSerialize(),
            $response['blocks'][2],
        );
    }

    /**
     * Test a simple roll with a description.
     */
    public function testWithDescription(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([3, 3, 3, 3]);

        $channel = Channel::factory()->make();
        $roll = new Generic('4d6 testing', 'user', $channel);

        $slack = $roll->forSlack()->jsonSerialize();
        self::assertEquals(
            (new Header('user rolled 12 for "testing"'))->jsonSerialize(),
            $slack['blocks'][0],
        );
        self::assertEquals(
            (new Text('Rolling: 4d6 = [3+3+3+3] = 12'))->jsonSerialize(),
            $slack['blocks'][1],
        );
        self::assertEquals(
            (new PlainText('Rolls: 3, 3, 3, 3'))->jsonSerialize(),
            $slack['blocks'][2],
        );

        $expected = '**user rolled 12 for "testing"**' . PHP_EOL
            . 'Rolling: 4d6 = [3+3+3+3] = 12' . PHP_EOL
            . '_Rolls: 3, 3, 3, 3_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }

    /**
     * Test a more complex calculation.
     */
    public function testWithCalculation(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 10)
            ->andReturn([10, 10]);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $roll = new Generic('4+2d10-1*10 foo', 'Bob', $channel);
        $slack = $roll->forSlack()->jsonSerialize();
        self::assertEquals(
            (new Header('Bob rolled 14 for "foo"'))->jsonSerialize(),
            $slack['blocks'][0],
        );
        self::assertEquals(
            (new Text('Rolling: 4+2d10-1*10 = 4+[10+10]-1*10 = 14'))->jsonSerialize(),
            $slack['blocks'][1],
        );
        self::assertEquals(
            (new PlainText('Rolls: 10, 10'))->jsonSerialize(),
            $slack['blocks'][2],
        );

        $expected = '**Bob rolled 14 for "foo"**' . PHP_EOL
            . 'Rolling: 4+2d10-1*10 = 4+[10+10]-1*10 = 14' . PHP_EOL
            . '_Rolls: 10, 10_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }
}
